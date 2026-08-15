<?php
namespace News\Commands;
use CMSMS\CLI\App;
use CMSMS\CLI\GetOptExt\Command;
use CMSMS\CLI\GetOptExt\Option;
use CMSMS\CLI\GetOptExt\GetOpt;
use GetOpt\Operand;

class CategoryEditCommand extends Command
{
    public function __construct(App $app)
    {
        parent::__construct($app, 'news-category-edit');
        $this->addOperand(new Operand('category', Operand::REQUIRED));
        $this->addOption(Option::Create(null, 'name', GetOpt::REQUIRED_ARGUMENT)->setDescription('Set category name'));
        $this->addOption(Option::Create(null, 'parent', GetOpt::REQUIRED_ARGUMENT)->setDescription('Set parent category name or ID (-1 for root)'));
    }

    public function getShortDescription()
    {
        return 'Edit a News category';
    }

    public function getLongDescription()
    {
        return 'Update a News category name or parent by ID or name';
    }

    public function handle()
    {
        $category = trim($this->getOperand('category')->getValue());
        $new_name = $this->getOption('name')->getValue();
        $parent = $this->getOption('parent')->getValue();

        if ($new_name === null && $parent === null) {
            throw new \RuntimeException('Please specify --name or --parent to change');
        }

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

        $updates = [];
        $params = [];
        $now = $db->DbTimeStamp(time());

        if ($new_name !== null) { $updates[] = 'news_category_name = ?'; $params[] = $new_name; }

        if ($parent !== null) {
            if ($parent == '-1') {
                $parent_id = -1;
            } elseif (is_numeric($parent)) {
                $check = $db->GetOne("SELECT news_category_id FROM {$prefix}module_news_categories WHERE news_category_id = ?", [(int) $parent]);
                if (!$check) throw new \RuntimeException("Parent category ID '$parent' not found");
                $parent_id = (int) $parent;
            } else {
                $check = $db->GetOne("SELECT news_category_id FROM {$prefix}module_news_categories WHERE news_category_name = ?", [$parent]);
                if (!$check) throw new \RuntimeException("Parent category '$parent' not found");
                $parent_id = (int) $check;
            }
            $updates[] = 'parent_id = ?';
            $params[] = $parent_id;
        }

        $updates[] = "modified_date = $now";
        $params[] = $cat_id;

        $db->Execute("UPDATE {$prefix}module_news_categories SET " . implode(', ', $updates) . " WHERE news_category_id = ?", $params);

        // Update hierarchy
        \news_admin_ops::UpdateHierarchyPositions();

        echo "Updated category $cat_id\n";
    }
}
