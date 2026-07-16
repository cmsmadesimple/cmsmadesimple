<?php
namespace News\Commands;
use CMSMS\CLI\App;
use CMSMS\CLI\GetOptExt\Command;
use CMSMS\CLI\GetOptExt\Option;
use CMSMS\CLI\GetOptExt\GetOpt;
use GetOpt\Operand;

class CategoryCreateCommand extends Command
{
    public function __construct(App $app)
    {
        parent::__construct($app, 'news-category-create');
        $this->addOperand(new Operand('name', Operand::REQUIRED));
        $this->addOption(Option::Create(null, 'parent', GetOpt::REQUIRED_ARGUMENT)->setDescription('Parent category name or ID (default: root)'));
    }

    public function getShortDescription()
    {
        return 'Create a News category';
    }

    public function getLongDescription()
    {
        return 'Create a new News category with an optional parent';
    }

    public function handle()
    {
        $name = trim($this->getOperand('name')->getValue());
        $parent = $this->getOption('parent')->getValue();

        $db = \cmsms()->GetDb();
        $prefix = \cms_db_prefix();

        // Check duplicate
        $exists = $db->GetOne("SELECT news_category_id FROM {$prefix}module_news_categories WHERE news_category_name = ?", [$name]);
        if ($exists) throw new \RuntimeException("Category '$name' already exists (ID: $exists)");

        // Resolve parent
        $parent_id = -1;
        if ($parent) {
            if (is_numeric($parent)) {
                $check = $db->GetOne("SELECT news_category_id FROM {$prefix}module_news_categories WHERE news_category_id = ?", [(int) $parent]);
                if (!$check) throw new \RuntimeException("Parent category ID '$parent' not found");
                $parent_id = (int) $parent;
            } else {
                $check = $db->GetOne("SELECT news_category_id FROM {$prefix}module_news_categories WHERE news_category_name = ?", [$parent]);
                if (!$check) throw new \RuntimeException("Parent category '$parent' not found");
                $parent_id = (int) $check;
            }
        }

        $now = $db->DbTimeStamp(time());
        $cat_id = $db->GenID($prefix . "module_news_categories_seq");

        $db->Execute(
            "INSERT INTO {$prefix}module_news_categories (news_category_id, news_category_name, parent_id, create_date, modified_date) VALUES (?, ?, ?, $now, $now)",
            [$cat_id, $name, $parent_id]
        );

        // Update hierarchy
        \news_admin_ops::UpdateHierarchyPositions();

        echo "Created category $cat_id ($name)\n";
    }
}
