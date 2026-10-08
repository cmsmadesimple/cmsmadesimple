<?php
namespace News\Commands;
use CMSMS\CLI\App;
use CMSMS\CLI\GetOptExt\Command;
use CMSMS\CLI\GetOptExt\Option;
use CMSMS\CLI\GetOptExt\GetOpt;
use GetOpt\Operand;

class FielddefCreateCommand extends Command
{
    public function __construct(App $app)
    {
        parent::__construct($app, 'news-fielddef-create');
        $this->addOperand(new Operand('name', Operand::REQUIRED));
        $this->addOption(Option::Create(null, 'type', GetOpt::REQUIRED_ARGUMENT)->setDescription('Field type: textbox, textarea, checkbox, dropdown, linkedfile, file (default: textbox)'));
        $this->addOption(Option::Create(null, 'max-length', GetOpt::REQUIRED_ARGUMENT)->setDescription('Max length (default: 255)'));
        $this->addOption(Option::Create(null, 'public')->setDescription('Make field public (visible in frontend form)'));
        $this->addOption(Option::Create(null, 'extra', GetOpt::REQUIRED_ARGUMENT)->setDescription('Extra options (e.g. dropdown values)'));
    }

    public function getShortDescription()
    {
        return 'Create a News custom field definition';
    }

    public function getLongDescription()
    {
        return 'Create a new custom field definition for News articles. Types: textbox, textarea, checkbox, dropdown, linkedfile, file';
    }

    public function handle()
    {
        $name = trim($this->getOperand('name')->getValue());
        $type = $this->getOption('type')->getValue() ?: 'textbox';
        $max_length = $this->getOption('max-length')->getValue();
        $public = $this->getOption('public')->getValue();
        $extra = $this->getOption('extra')->getValue();

        $valid_types = ['textbox', 'textarea', 'checkbox', 'dropdown', 'linkedfile', 'file'];
        if (!in_array($type, $valid_types)) {
            throw new \RuntimeException("Invalid type '$type'. Valid: " . implode(', ', $valid_types));
        }

        $db = \cmsms()->GetDb();
        $prefix = \cms_db_prefix();

        // Check duplicate
        $exists = $db->GetOne("SELECT id FROM {$prefix}module_news_fielddefs WHERE name = ?", [$name]);
        if ($exists) throw new \RuntimeException("Field '$name' already exists (ID: $exists)");

        // Get next order
        $max_order = (int) $db->GetOne("SELECT MAX(item_order) FROM {$prefix}module_news_fielddefs");
        $now = $db->DbTimeStamp(time());

        $db->Execute(
            "INSERT INTO {$prefix}module_news_fielddefs (name, type, max_length, item_order, public, extra, create_date, modified_date) VALUES (?, ?, ?, ?, ?, ?, $now, $now)",
            [$name, $type, $max_length ? (int) $max_length : 255, $max_order + 1, $public ? 1 : 0, $extra ?: '']
        );

        $fd_id = $db->Insert_ID();
        echo "Created field definition $fd_id ($name, type: $type)\n";
    }
}
