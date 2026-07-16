<?php
namespace News\Commands;
use CMSMS\CLI\App;
use CMSMS\CLI\GetOptExt\Command;
use CMSMS\CLI\GetOptExt\Option;
use CMSMS\CLI\GetOptExt\GetOpt;
use GetOpt\Operand;

class ArticleCreateCommand extends Command
{
    public function __construct(App $app)
    {
        parent::__construct($app, 'news-create');
        $this->addOperand(new Operand('title', Operand::REQUIRED));
        $this->addOption(Option::Create(null, 'content', GetOpt::REQUIRED_ARGUMENT)->setDescription('Article body (HTML)'));
        $this->addOption(Option::Create(null, 'file', GetOpt::REQUIRED_ARGUMENT)->setDescription('Read content from file'));
        $this->addOption(Option::Create(null, 'summary', GetOpt::REQUIRED_ARGUMENT)->setDescription('Article summary'));
        $this->addOption(Option::Create(null, 'url', GetOpt::REQUIRED_ARGUMENT)->setDescription('Pretty URL slug'));
        $this->addOption(Option::Create(null, 'category', GetOpt::REQUIRED_ARGUMENT)->setDescription('Category name (default: General)'));
        $this->addOption(Option::Create(null, 'status', GetOpt::REQUIRED_ARGUMENT)->setDescription('Status: published or draft (default: published)'));
        $this->addOption(Option::Create(null, 'news-date', GetOpt::REQUIRED_ARGUMENT)->setDescription('Publish date (Y-m-d H:i:s, default: now)'));
        $this->addOption(Option::Create(null, 'start-date', GetOpt::REQUIRED_ARGUMENT)->setDescription('Start date (Y-m-d H:i:s)'));
        $this->addOption(Option::Create(null, 'end-date', GetOpt::REQUIRED_ARGUMENT)->setDescription('End date (Y-m-d H:i:s)'));
        $this->addOption(Option::Create(null, 'extra', GetOpt::REQUIRED_ARGUMENT)->setDescription('Extra field value'));
        $this->addOption(Option::Create(null, 'searchable', GetOpt::REQUIRED_ARGUMENT)->setDescription('Searchable: 1 or 0 (default: 1)'));
        $this->addOption(Option::Create(null, 'set-field', GetOpt::MULTIPLE_ARGUMENT)->setDescription('Set custom field: name=value (repeatable)'));
    }

    public function getShortDescription()
    {
        return 'Create a News article';
    }

    public function getLongDescription()
    {
        return 'Create a new News article with title, content, category, URL, and custom field values';
    }

    public function handle()
    {
        $title = trim($this->getOperand('title')->getValue());
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

        // Resolve category
        $cat_id = 1; // Default: General
        if ($category) {
            $found = $db->GetOne("SELECT news_category_id FROM {$prefix}module_news_categories WHERE news_category_name = ?", [$category]);
            if (!$found) throw new \RuntimeException("Category '$category' not found");
            $cat_id = (int) $found;
        }

        // Defaults
        if (!$status) $status = 'published';
        if (!$url) $url = munge_string_to_url($title, true);
        if ($searchable === null) $searchable = 1;
        $now = $db->DbTimeStamp(time());

        // Generate ID
        $article_id = $db->GenID($prefix . "module_news_seq");

        // Build news_date
        $news_date_sql = $news_date ? $db->DbTimeStamp(strtotime($news_date)) : $now;
        $start_date_sql = $start_date ? $db->DbTimeStamp(strtotime($start_date)) : 'NULL';
        $end_date_sql = $end_date ? $db->DbTimeStamp(strtotime($end_date)) : 'NULL';

        $sql = "INSERT INTO {$prefix}module_news
                (news_id, news_category_id, news_title, news_data, news_date, summary, start_time, end_time, status, news_extra, news_url, searchable, author_id, create_date, modified_date)
                VALUES (?, ?, ?, ?, $news_date_sql, ?, $start_date_sql, $end_date_sql, ?, ?, ?, ?, ?, $now, $now)";

        $params = [
            $article_id,
            $cat_id,
            $title,
            $content ?: '',
            $summary ?: '',
            $status,
            $extra ?: '',
            $url,
            (int) $searchable,
            1 // author_id (admin)
        ];

        $dbr = $db->Execute($sql, $params);
        if (!$dbr) throw new \RuntimeException("Failed to insert article: " . $db->ErrorMsg());

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
                $db->Execute("INSERT INTO {$prefix}module_news_fieldvals (news_id, fielddef_id, value, create_date, modified_date) VALUES (?, ?, ?, $now, $now)",
                    [$article_id, (int) $fd_id, $fd_value]);
            }
        }

        // Register static route
        if ($url && $status === 'published') {
            \news_admin_ops::register_static_route($url, $article_id);
        }

        echo "Created article $article_id ($url)\n";
    }
}
