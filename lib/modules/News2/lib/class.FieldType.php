<?php
namespace News2;

abstract class FieldType
{
    abstract public function getName() : string;

    // render specific html content for this field in the edit fielddef form
    abstract public function renderForEditor(FieldDef $def);

    // handle response in the edit fielddef form
    abstract public function handleEditorResponse(Fielddef $def, array $data) : FieldDef;

    // render the field in the edit article form
    abstract public function renderForArticle(FieldDef $def, $value);

    // returns a value for the field or null
    abstract public function handleForArticle(FieldDef $def, array $formdata);
} // class