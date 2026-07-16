<?php
namespace News\Commands;
use CMSMS\CLI\App;
use CMSMS\CLI\GetOptExt\Command;
use CMSMS\CLI\GetOptExt\Option;
use CMSMS\CLI\GetOptExt\GetOpt;
use GetOpt\Operand;

class FielddefDeleteCommand extends Command
{
    public function __construct(App $app)
    {
        parent::__construct($app, 'news-fielddef-delete');
        $this->addOperand(new Operand('field', Operand::REQUIRED));
    }

    public function getShortDescription()
    {
        return 'Delete a News custom field definition';
    }

    public function getLongDescription()
    {
        return 'Delete a News custom field definition by ID or name. Also removes all stored values for that field.';
    }

    public function handle()
    {
        $field = trim($this->getOperand('field')->getValue());

        $db = \cmsms()->GetDb();
        $prefix = \cms_db_prefix();

        // Resolve field
        if (is_numeric($field)) {
            $row = $db->GetRow("SELECT id, name FROM {$prefix}module_news_fielddefs WHERE id = ?", [(int) $field]);
        } else {
            $row = $db->GetRow("SELECT id, name FROM {$prefix}module_news_fielddefs WHERE name = ?", [$field]);
        }
        if (!$row) throw new \RuntimeException("Field definition '$field' not found");
        $fd_id = (int) $row['id'];

        // Delete all field values
        $db->Execute("DELETE FROM {$prefix}module_news_fieldvals WHERE fielddef_id = ?", [$fd_id]);

        // Delete the definition
        $db->Execute("DELETE FROM {$prefix}module_news_fielddefs WHERE id = ?", [$fd_id]);

        echo "Deleted field definition $fd_id ({$row['name']})\n";
    }
}
