<?php

namespace InertiaStudio;

class Field
{
    public static function text(string $name): Fields\TextField
    {
        return new Fields\TextField($name);
    }

    public static function email(string $name): Fields\EmailField
    {
        return new Fields\EmailField($name);
    }

    public static function password(string $name): Fields\PasswordField
    {
        return new Fields\PasswordField($name);
    }

    public static function url(string $name): Fields\UrlField
    {
        return new Fields\UrlField($name);
    }

    public static function tel(string $name): Fields\TelField
    {
        return new Fields\TelField($name);
    }

    public static function number(string $name): Fields\NumberField
    {
        return new Fields\NumberField($name);
    }

    public static function stepper(string $name): Fields\StepperField
    {
        return new Fields\StepperField($name);
    }

    public static function textarea(string $name): Fields\TextareaField
    {
        return new Fields\TextareaField($name);
    }

    public static function select(string $name): Fields\SelectField
    {
        return new Fields\SelectField($name);
    }

    public static function toggle(string $name): Fields\ToggleField
    {
        return new Fields\ToggleField($name);
    }

    public static function checkbox(string $name): Fields\CheckboxField
    {
        return new Fields\CheckboxField($name);
    }

    public static function checkboxList(string $name): Fields\CheckboxListField
    {
        return new Fields\CheckboxListField($name);
    }

    public static function radio(string $name): Fields\RadioField
    {
        return new Fields\RadioField($name);
    }

    public static function toggleButtons(string $name): Fields\ToggleButtonsField
    {
        return new Fields\ToggleButtonsField($name);
    }

    public static function date(string $name): Fields\DateField
    {
        return new Fields\DateField($name);
    }

    public static function time(string $name): Fields\TimeField
    {
        return new Fields\TimeField($name);
    }

    public static function dateRange(string $name): Fields\DateRangeField
    {
        return new Fields\DateRangeField($name);
    }

    public static function colorPicker(string $name): Fields\ColorPickerField
    {
        return new Fields\ColorPickerField($name);
    }

    public static function fileUpload(string $name): Fields\FileUploadField
    {
        return new Fields\FileUploadField($name);
    }

    public static function imageUpload(string $name): Fields\ImageUploadField
    {
        return new Fields\ImageUploadField($name);
    }

    public static function tags(string $name): Fields\TagsField
    {
        return new Fields\TagsField($name);
    }

    public static function keyValue(string $name): Fields\KeyValueField
    {
        return new Fields\KeyValueField($name);
    }

    public static function repeater(string $name): Fields\RepeaterField
    {
        return new Fields\RepeaterField($name);
    }

    public static function hidden(string $name): Fields\HiddenField
    {
        return new Fields\HiddenField($name);
    }

    public static function placeholder(string $name): Fields\PlaceholderField
    {
        return new Fields\PlaceholderField($name);
    }

    public static function slug(string $name): Fields\SlugField
    {
        return new Fields\SlugField($name);
    }

    public static function money(string $name): Fields\MoneyField
    {
        return new Fields\MoneyField($name);
    }

    public static function percent(string $name): Fields\PercentField
    {
        return new Fields\PercentField($name);
    }

    public static function belongsTo(string $name): Fields\BelongsToField
    {
        return new Fields\BelongsToField($name);
    }

    public static function richEditor(string $name): Fields\RichEditorField
    {
        return new Fields\RichEditorField($name);
    }

    public static function otp(string $name): Fields\OtpField
    {
        return new Fields\OtpField($name);
    }

    public static function masked(string $name): Fields\MaskedField
    {
        return new Fields\MaskedField($name);
    }

    public static function rating(string $name): Fields\RatingField
    {
        return new Fields\RatingField($name);
    }

    public static function code(string $name): Fields\CodeField
    {
        return new Fields\CodeField($name);
    }

    public static function markdownEditor(string $name): Fields\MarkdownEditorField
    {
        return new Fields\MarkdownEditorField($name);
    }
}
