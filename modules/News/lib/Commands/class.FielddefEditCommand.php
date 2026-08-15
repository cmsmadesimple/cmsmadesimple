<?php
namespace News\Commands;
use CMSMS\CLI\App;
use CMSMS\CLI\GetOptExt\Command;
use CMSMS\CLI\GetOptExt\Option;
use CMSMS\CLI\GetOptExt\GetOpt;
use GetOpt\Operand;

class FielddefEditCommand extends Command
{
    public function __construct(App $app)
    {
        parent::__construct($app, 'news-fielddef-edit');
        $this->addOperand(new Operand('field', Operand::REQUIRED));
        $this->addOption(Option::Create(null, 'name', GetOpt::REQUIRED_ARGUMENT)->setDescription('Set field name'));
        $this->addOption(Option::Create(null, 'type', GetOpt::REQUIRED_ARGUMENT)->setDescription('Set field type'));
        $this->addOption(Option::Create(null, 'max-length', GetOpt::REQUIRED_ARGUMENT)->setDescription('Set max length'));
        $this->addOption(Option::Create(null, 'public', GetOpt::REQUIRED_ARGUMENT)->setDescription('Set public: 1 or 0'));
        $this->addOption(Option::Create(null, 'extra', GetOpt::REQUIRED_ARGUMENT)->setDescription('Set extra options'));
    }

    public function getShortDescription()
    {
        return 'Edit a News custom field definition';
    }

    public function getLongDescription()
    {
        return 'Update a News custom field definition by ID or name';
    }

    public function handle()
    {
        $field = trim($this->getOperand('field')->getValue());
        $new_name = $this->getOption('name')->getValue();
        $type = $this->getOption('type')->getValue();
        $max_length = $this->getOption('max-length')->getValue();
        $public = $this->getOption('public')->getValue();
        $extra = $this->getOption('extra')->getValue();

        if ($new_name === null && $type === null && $max_length === null && $public === null && $extra === null) {
            throw new \RuntimeException('Please specify at least one option to change');
        }

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

        if ($type) {
            $valid_types = ['textbox', 'textarea', 'checkbox', 'dropdown', 'linkedfile', 'file'];
            if (!in_array($type, $valid_types)) {
                throw new \RuntimeException("Invalid type '$type'. Valid: " . implode(', ', $valid_types));
            }
        }

        $updates = [];
        $params = [];
        $now = $db->DbTimeStamp(time());

        if ($new_name !== null) { $updates[] = 'name = ?'; $params[] = $new_name; }
        if ($type !== null) { $updates[] = 'type = ?'; $params[] = $type; }
        if ($max_length !== null) { $updates[] = 'max_length = ?'; $params[] = (int) $max_length; }
        if ($public !== null) { $updates[] = 'public = ?'; $params[] = (int) $public; }
        if ($extra !== null) { $updates[] = 'extra = ?'; $params[] = $extra; }

        $updates[] = "modified_date = $now";
        $params[] = $fd_id;

        $db->Execute("UPDATE {$prefix}module_news_fielddefs SET " . implode(', ', $updates) . " WHERE id = ?", $params);

        echo "Updated field definition $fd_id\n";
    }
}
