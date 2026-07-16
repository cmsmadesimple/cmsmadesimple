<?php
namespace News\Commands;
use CMSMS\CLI\App;
use CMSMS\CLI\GetOptExt\Command;
use CMSMS\CLI\GetOptExt\Option;
use CMSMS\CLI\GetOptExt\GetOpt;
use GetOpt\Operand;

class ArticleEditCommand extends Command
{
    public function __construct(App $app)
    {
        parent::__construct($app, 'news-edit');
        $this->addOperand(new Operand('article', Operand::REQUIRED));
        $this->addOption(Option::Create(null, 'title', GetOpt::REQUIRED_ARGUMENT)->setDescription('Set article title'));
        $this->addOption(Option::Create(null, 'content', GetOpt::REQUIRED_ARGUMENT)->setDescription('Set article body (HTML)'));
        $this->addOption(Option::Create(null, 'file', GetOpt::REQUIRED_ARGUMENT)->setDescription('Read content from file'));
        $this->addOption(Option::Create(null, 'summary', GetOpt::REQUIRED_ARGUMENT)->setDescription('Set summary'));
        $this->addOption(Option::Create(null, 'url', GetOpt::REQUIRED_ARGUMENT)->setDescription('Set pretty URL slug'));
        $this->addOption(Option::Create(null, 'category', GetOpt::REQUIRED_ARGUMENT)->setDescription('Set category name'));
        $this->addOption(Option::Create(null, 'status', GetOpt::REQUIRED_ARGUMENT)->setDescription('Set status: published or draft'));
        $this->addOption(Option::Create(null, 'news-date', GetOpt::REQUIRED_ARGUMENT)->setDescription('Set publish date (Y-m-d H:i:s)'));
        $this->addOption(Option::Create(null, 'start-date', GetOpt::REQUIRED_ARGUMENT)->setDescription('Set start date (Y-m-d H:i:s)'));
        $this->addOption(Option::Create(null, 'end-date', GetOpt::REQUIRED_ARGUMENT)->setDescription('Set end date (Y-m-d H:i:s)'));
        $this->addOption(Option::Create(null, 'extra', GetOpt::REQUIRED_ARGUMENT)->setDescription('Set extra field'));
        $this->addOption(Option::Create(null, 'searchable', GetOpt::REQUIRED_ARGUMENT)->setDescription('Set searchable: 1 or 0'));
        $this->addOption(Option::Create(null, 'set-field', GetOpt::MULTIPLE_ARGUMENT)->setDescription('Set custom field: name=value (repeatable)'));
    }

    public function getShortDescription()
    {
        return 'Edit a News article';
    }

    public function getLongDescription()
    {
        return 'Update an existing News article by ID or URL slug. Specify fields to change.';
    }

    public function handle()
    {
        $article = trim($this->getOperand('article')->getValue());
        $new_title = $this->getOption('title')->getValue();
        $content = $this->getOption('content')->getValue();
        $file = $this->getOption('file')->getValue();
        $summary = $this->getOption('summary')->getValue();
        $url = $this->getOption('url')->getValue();
        $category = $this->getOption('category')->getValue();
        $status = $this->getOption('status')->getValue();
        $news_date = $this->getOption('news-date')->getValue();
        $start_date = $this->getOption('start-date')->getValue();
        $end_date = $this->getOption('end-date')->getValue();
        $extra = $this->getOption('extra')->getValue();
        $searchable = $this->getOption('searchable')->getValue();
        $fields = $this->getOption('set-field')->getValue();

        // Content from file
        if ($file) {
            if (!is_file($file)) throw new \RuntimeException("File not found: $file");
            $content = file_get_contents($file);
        }

        $db = \cmsms()->GetDb();
        $prefix = \cms_db_prefix();

        // Resolve article by ID or URL slug
        if (is_numeric($article)) {
            $row = $db->GetRow("SELECT news_id, news_title, news_url FROM {$prefix}module_news WHERE news_id = ?", [(int) $article]);
        } else {
            $row = $db->GetRow("SELECT news_id, news_title, news_url FROM {$prefix}module_news WHERE news_url = ?", [$article]);
        }
        if (!$row) throw new \RuntimeException("Article '$article' not found");
        $article_id = (int) $row['news_id'];

        // Build UPDATE
        $updates = [];
        $params = [];
        $now = $db->DbTimeStamp(time());

        if ($new_title !== null) { $updates[] = 'news_title = ?'; $params[] = $new_title; }
        if ($content !== null) { $updates[] = 'news_data = ?'; $params[] = $content; }
        if ($summary !== null) { $updates[] = 'summary = ?'; $params[] = $summary; }
        if ($url !== null) { $updates[] = 'news_url = ?'; $params[] = $url; }
        if ($status !== null) { $updates[] = 'status = ?'; $params[] = $status; }
        if ($extra !== null) { $updates[] = 'news_extra = ?'; $params[] = $extra; }
        if ($searchable !== null) { $updates[] = 'searchable = ?'; $params[] = (int) $searchable; }

        if ($category) {
            $cat_id = $db->GetOne("SELECT news_category_id FROM {$prefix}module_news_categories WHERE news_category_name = ?", [$category]);
            if (!$cat_id) throw new \RuntimeException("Category '$category' not found");
            $updates[] = 'news_category_id = ?';
            $params[] = (int) $cat_id;
        }

        if ($news_date) { $updates[] = "news_date = " . $db->DbTimeStamp(strtotime($news_date)); }
        if ($start_date) { $updates[] = "start_time = " . $db->DbTimeStamp(strtotime($start_date)); }
        if ($end_date) { $updates[] = "end_time = " . $db->DbTimeStamp(strtotime($end_date)); }

        if (empty($updates) && empty($fields)) {
            throw new \RuntimeException('Please specify at least one option to change');
        }

        if (!empty($updates)) {
            $updates[] = "modified_date = $now";
            $params[] = $article_id;
            $sql = "UPDATE {$prefix}module_news SET " . implode(', ', $updates) . " WHERE news_id = ?";
            $db->Execute($sql, $params);
        }

        // Set custom field values
        if (!empty($fields)) {
            foreach ($fields as $fv) {
                $parts = explode('=', $fv, 2);
                if (count($parts) !== 2) continue;
                $fd_name = trim($parts[0]);
                $fd_value = trim($parts[1]);

                $fd_id = $db->GetOne("SELECT id FROM {$prefix}module_news_fielddefs WHERE name = ?", [$fd_name]);
                if (!$fd_id) {
                    echo "WARNING: Custom field '$fd_name' not found, skipping.\n";
                    continue;
                }

                $existing = $db->GetOne("SELECT value FROM {$prefix}module_news_fieldvals WHERE news_id = ? AND fielddef_id = ?", [$article_id, (int) $fd_id]);
                if ($existing !== false && $existing !== null) {
                    $db->Execute("UPDATE {$prefix}module_news_fieldvals SET value = ?, modified_date = $now WHERE news_id = ? AND fielddef_id = ?",
                        [$fd_value, $article_id, (int) $fd_id]);
                } else {
                    $db->Execute("INSERT INTO {$prefix}module_news_fieldvals (news_id, fielddef_id, value, create_date, modified_date) VALUES (?, ?, ?, $now, $now)",
                        [$article_id, (int) $fd_id, $fd_value]);
                }
            }
        }

        // Re-register route if URL changed
        if ($url !== null && $url !== $row['news_url']) {
            \cms_route_manager::del_static($row['news_url'], 'News');
            \news_admin_ops::register_static_route($url, $article_id);
        }

        echo "Updated article $article_id\n";
    }
}
