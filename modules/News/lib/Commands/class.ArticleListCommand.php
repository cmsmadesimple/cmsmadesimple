<?php
namespace News\Commands;
use CMSMS\CLI\App;
use CMSMS\CLI\GetOptExt\Command;
use CMSMS\CLI\GetOptExt\Option;
use CMSMS\CLI\GetOptExt\GetOpt;

class ArticleListCommand extends Command
{
    public function __construct(App $app)
    {
        parent::__construct($app, 'news-list');
        $this->addOption(Option::Create(null, 'category', GetOpt::REQUIRED_ARGUMENT)->setDescription('Filter by category name'));
        $this->addOption(Option::Create(null, 'status', GetOpt::REQUIRED_ARGUMENT)->setDescription('Filter by status: published, draft'));
        $this->addOption(Option::Create(null, 'limit', GetOpt::REQUIRED_ARGUMENT)->setDescription('Max articles (default 50)'));
        $this->addOption(Option::Create(null, 'output-json')->setDescription('Output in JSON format'));
    }

    public function getShortDescription()
    {
        return 'List News articles';
    }

    public function getLongDescription()
    {
        return 'Display a list of News articles with optional category and status filters';
    }

    public function handle()
    {
        $category = $this->getOption('category')->getValue();
        $status = $this->getOption('status')->getValue();
        $limit = (int) $this->getOption('limit')->getValue();
        $json = $this->getOption('output-json')->getValue();

        if ($limit < 1) $limit = 50;

        $db = \cmsms()->GetDb();
        $prefix = \cms_db_prefix();

        $sql = "SELECT n.news_id, n.news_title, n.news_url, n.status, n.news_date, c.news_category_name AS category
                FROM {$prefix}module_news n
                LEFT JOIN {$prefix}module_news_categories c ON c.news_category_id = n.news_category_id
                WHERE 1=1";
        $params = [];

        if ($category) {
            $sql .= " AND c.news_category_name = ?";
            $params[] = $category;
        }
        if ($status) {
            $sql .= " AND n.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY n.news_date DESC LIMIT $limit";
        $rows = $db->GetArray($sql, $params);

        if (empty($rows)) {
            if (!$json) echo "No articles found.\n";
            return;
        }

        if ($json) {
            $out = [];
            foreach ($rows as $row) {
                $row['news_id'] = (int) $row['news_id'];
                $out[] = $row;
            }
            echo json_encode($out, JSON_PRETTY_PRINT) . "\n";
            return;
        }

        $fmt = "%-5s %-50s %-25s %-10s %-19s\n";
        printf($fmt, 'ID', 'Title', 'URL', 'Status', 'Date');
        printf($fmt, '--', '-----', '---', '------', '----');
        foreach ($rows as $row) {
            printf($fmt,
                $row['news_id'],
                substr($row['news_title'], 0, 50),
                substr($row['news_url'], 0, 25),
                $row['status'],
                substr($row['news_date'], 0, 19)
            );
        }
    }
}
