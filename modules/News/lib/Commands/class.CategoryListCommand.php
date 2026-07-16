<?php
namespace News\Commands;
use CMSMS\CLI\App;
use CMSMS\CLI\GetOptExt\Command;
use CMSMS\CLI\GetOptExt\Option;
use CMSMS\CLI\GetOptExt\GetOpt;

class CategoryListCommand extends Command
{
    public function __construct(App $app)
    {
        parent::__construct($app, 'news-category-list');
        $this->addOption(Option::Create(null, 'output-json')->setDescription('Output in JSON format'));
    }

    public function getShortDescription()
    {
        return 'List News categories';
    }

    public function getLongDescription()
    {
        return 'Display all News categories with their ID, name, and parent';
    }

    public function handle()
    {
        $json = $this->getOption('output-json')->getValue();

        $db = \cmsms()->GetDb();
        $prefix = \cms_db_prefix();

        $rows = $db->GetArray("SELECT news_category_id, news_category_name, parent_id, item_order FROM {$prefix}module_news_categories ORDER BY item_order, news_category_name");

        if (empty($rows)) {
            if (!$json) echo "No categories found.\n";
            return;
        }

        if ($json) {
            $out = [];
            foreach ($rows as $row) {
                $row['news_category_id'] = (int) $row['news_category_id'];
                $row['parent_id'] = (int) $row['parent_id'];
                $row['item_order'] = (int) $row['item_order'];
                $out[] = $row;
            }
            echo json_encode($out, JSON_PRETTY_PRINT) . "\n";
            return;
        }

        $fmt = "%-5s %-40s %-10s %-5s\n";
        printf($fmt, 'ID', 'Name', 'Parent', 'Order');
        printf($fmt, '--', '----', '------', '-----');
        foreach ($rows as $row) {
            printf($fmt, $row['news_category_id'], substr($row['news_category_name'], 0, 40), $row['parent_id'], $row['item_order']);
        }
    }
}
