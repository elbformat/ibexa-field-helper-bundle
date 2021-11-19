<?php
declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\FieldHelper;

use Elbformat\FieldHelperBundle\Exception\InvalidFieldTypeException;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentStruct;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\FieldType\EmailAddress\Value as MailValue;
use eZ\Publish\Core\FieldType\TextBlock\Value as TextBlockValue;
use eZ\Publish\Core\FieldType\TextLine\Value as TextLineValue;

/**
 * Handles ezstring,eztext and ezemail
 *
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class TextFieldHelper extends AbstractFieldHelper
{
    public static function getName(): string
    {
        return self::class;
    }

    public function getString(Content $content, string $fieldName): ?string
    {
        $field = $this->getField($content, $fieldName);

        return $this->getStringFieldValue($field);
    }

    public function updateString(ContentStruct $struct, string $fieldName, string $value, ?Content $content = null): bool
    {
        // No changes
        if (null !== $content) {
            $field = $this->getField($content, $fieldName);
            if ($this->isStringEqual($field, $value)) {
                return false;
            }
        }
        $struct->setField($fieldName, $value);

        return true;
    }

    protected function isStringEqual(Field $field, ?string $value): bool
    {
        $fieldVal = $this->getStringFieldValue($field);

        return $fieldVal === $value;
    }

    protected function getStringFieldValue(Field $field): ?string
    {
        switch (\get_class($field->value)) {
            case MailValue::class:
                return $field->value->email ?? null;
            case TextLineValue::class:
            case TextBlockValue::class:
                return $field->value->text ?? null;
            default:
                $allowed = [TextLineValue::class, TextBlockValue::class, MailValue::class];
                throw InvalidFieldTypeException::fromActualAndExpected($field->value, $allowed);
        }
    }
}
