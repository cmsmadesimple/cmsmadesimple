<?php
namespace News\Commands;
use CMSMS\CLI\App;
use CMSMS\CLI\GetOptExt\Command;
use CMSMS\CLI\GetOptExt\Option;
use CMSMS\CLI\GetOptExt\GetOpt;
use GetOpt\Operand;

class ArticleDeleteCommand extends Command
{
    public function __construct(App $app)
    {
        parent::__construct($app, 'news-delete');
        $this->addOperand(new Operand('article', Operand::REQUIRED));
    }

    public function getShortDescription()
    {
        return 'Delete a News article';
    }

    public function getLongDescription()
    {
        return 'Delete a News article by ID or URL slug. Removes the article, its field values, and its static route.';
    }

    public function handle()
    {
        $article = trim($this->getOperand('article')->getValue());

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

        // Delete field values
        $db->Execute("DELETE FROM {$prefix}module_news_fieldvals WHERE news_id = ?", [$article_id]);

        // Delete article
        $db->Execute("DELETE FROM {$prefix}module_news WHERE news_id = ?", [$article_id]);

        // Remove static route
        if ($row['news_url']) {
            \cms_route_manager::del_static($row['news_url'], 'News');
        }

        echo "Deleted article $article_id ({$row['news_title']})\n";
    }
}
