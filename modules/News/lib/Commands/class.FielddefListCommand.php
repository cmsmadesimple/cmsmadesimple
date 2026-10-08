<?php
namespace News\Commands;
use CMSMS\CLI\App;
use CMSMS\CLI\GetOptExt\Command;
use CMSMS\CLI\GetOptExt\Option;
use CMSMS\CLI\GetOptExt\GetOpt;

class FielddefListCommand extends Command
{
    public function __construct(App $app)
    {
        parent::__construct($app, 'news-fielddef-list');
        $this->addOption(Option::Create(null, 'output-json')->setDescription('Output in JSON format'));
    }

    public function getShortDescription()
    {
        return 'List News custom field definitions';
    }

    public function getLongDescription()
    {
        return 'Display all custom field definitions for News articles';
    }

    public function handle()
    {
        $json = $this->getOption('output-json')->getValue();

        $db = \cmsms()->GetDb();
        $prefix = \cms_db_prefix();

        $rows = $db->GetArray("SELECT id, name, type, max_length, item_order, public FROM {$prefix}module_news_fielddefs ORDER BY item_order");

        if (empty($rows)) {
            if (!$json) echo "No field definitions found.\n";
            return;
        }

        if ($json) {
            $out = [];
            foreach ($rows as $row) {
                $row['id'] = (int) $row['id'];
                $row['max_length'] = (int) $row['max_length'];
                $row['item_order'] = (int) $row['item_order'];
                $row['public'] = (int) $row['public'];
                $out[] = $row;
            }
            echo json_encode($out, JSON_PRETTY_PRINT) . "\n";
            return;
        }

        $fmt = "%-5s %-30s %-12s %-10s %-6s %-6s\n";
        printf($fmt, 'ID', 'Name', 'Type', 'MaxLen', 'Order', 'Public');
        printf($fmt, '--', '----', '----', '------', '-----', '------');
        foreach ($rows as $row) {
            printf($fmt, $row['id'], substr($row['name'], 0, 30), $row['type'], $row['max_length'] ?: '-', $row['item_order'], $row['public']);
        }
    }
}
