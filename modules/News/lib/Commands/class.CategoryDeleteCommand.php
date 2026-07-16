<?php
namespace News\Commands;
use CMSMS\CLI\App;
use CMSMS\CLI\GetOptExt\Command;
use CMSMS\CLI\GetOptExt\Option;
use CMSMS\CLI\GetOptExt\GetOpt;
use GetOpt\Operand;

class CategoryDeleteCommand extends Command
{
    public function __construct(App $app)
    {
        parent::__construct($app, 'news-category-delete');
        $this->addOperand(new Operand('category', Operand::REQUIRED));
        $this->addOption(Option::Create(null, 'force')->setDescription('Force delete even if category has articles'));
    }

    public function getShortDescription()
    {
        return 'Delete a News category';
    }

    public function getLongDescription()
    {
        return 'Delete a News category by ID or name. Refuses if articles exist unless --force is used.';
    }

    public function handle()
    {
        $category = trim($this->getOperand('category')->getValue());
        $force = $this->getOption('force')->getValue();

        $db = \cmsms()->GetDb();
        $prefix = \cms_db_prefix();

        // Resolve category
        if (is_numeric($category)) {
            $row = $db->GetRow("SELECT news_category_id, news_category_name FROM {$prefix}module_news_categories WHERE news_category_id = ?", [(int) $category]);
        } else {
            $row = $db->GetRow("SELECT news_category_id, news_category_name FROM {$prefix}module_news_categories WHERE news_category_name = ?", [$category]);
        }
        if (!$row) throw new \RuntimeException("Category '$category' not found");
        $cat_id = (int) $row['news_category_id'];

        // Check for articles in this category
        $count = (int) $db->GetOne("SELECT COUNT(*) FROM {$prefix}module_news WHERE news_category_id = ?", [$cat_id]);
        if ($count > 0 && !$force) {
            throw new \RuntimeException("Category '{$row['news_category_name']}' has $count article(s). Use --force to delete anyway.");
        }

        // If force, move articles to first available category
        if ($count > 0) {
            $fallback = $db->GetOne("SELECT news_category_id FROM {$prefix}module_news_categories WHERE news_category_id != ? ORDER BY news_category_id LIMIT 1", [$cat_id]);
            if ($fallback) {
                $db->Execute("UPDATE {$prefix}module_news SET news_category_id = ? WHERE news_category_id = ?", [(int) $fallback, $cat_id]);
                echo "Moved $count article(s) to category $fallback\n";
            }
        }

        $db->Execute("DELETE FROM {$prefix}module_news_categories WHERE news_category_id = ?", [$cat_id]);
        \news_admin_ops::UpdateHierarchyPositions();

        echo "Deleted category $cat_id ({$row['news_category_name']})\n";
    }
}
