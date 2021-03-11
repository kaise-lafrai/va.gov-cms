<?php

namespace Drupal\va_gov_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateSkipProcessException;
use Drupal\migrate\MigrateSkipRowException;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * If the source evaluates to a configured value, skip processing or whole row.
 *
 * @MigrateProcessPlugin(
 *   id = "va_skip_on_substr"
 * )
 *
 * Available configuration keys:
 * - value: An single value or array of values against which the source value
 *   should be compared.
 * - not_equals: (optional) If set, skipping occurs when values are not equal.
 * - method: What to do if the input value equals to value given in
 *   configuration key value. Possible values:
 *   - row: Skips the entire row.
 *   - process: Prevents further processing of the input property
 *
 * @codingStandardsIgnoreStart
 *
 * Examples:
 *
 * Example usage with minimal configuration:
 * @code
 *   type:
 *     plugin: skip_on_value
 *     source: content_type
 *     method: process
 *     value: blog
 * @endcode
 * The above example will skip further processing of the input property if
 * the content_type source field equals "blog".
 *
 * Example usage with full configuration:
 * @code
 *   type:
 *     plugin: skip_on_value
 *     not_equals: true
 *     source: content_type
 *     method: row
 *     value:
 *       - article
 *       - testimonial
 * @endcode
 * The above example will skip processing any row for which the source row's
 * content type field is not "article" or "testimonial".
 *
 * @codingStandardsIgnoreEnd
 */
class VASkipOnSubstr extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function row($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (empty($this->configuration['value']) && !array_key_exists('value', $this->configuration)) {
      throw new MigrateException('Skip on value plugin is missing value configuration.');
    }

    if (is_array($this->configuration['value'])) {
      $value_in_array = FALSE;
      $not_equals = isset($this->configuration['not_equals']);

      foreach ($this->configuration['value'] as $skipValue) {
        $value_in_array |= $this->compareValue($value, $skipValue, $this->configuration['case_sensitive']);
      }

      if (($not_equals && !$value_in_array) || (!$not_equals && $value_in_array)) {
        throw new MigrateSkipRowException();
      }
    }
    elseif ($this->compareValue($value, $this->configuration['value'], $this->configuration['case_sensitive'], !isset($this->configuration['not_equals']))) {
      throw new MigrateSkipRowException();
    }

    return $value;
  }

  /**
   * {@inheritdoc}
   */
  public function process($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (empty($this->configuration['value']) && !array_key_exists('value', $this->configuration)) {
      throw new MigrateException('Skip on value plugin is missing value configuration.');
    }

    if (is_array($this->configuration['value'])) {
      $value_in_array = FALSE;
      $not_equals = isset($this->configuration['not_equals']);

      foreach ($this->configuration['value'] as $skipValue) {
        $value_in_array |= $this->compareValue($value, $skipValue, $this->configuration['case_sensitive']);
      }

      if (($not_equals && !$value_in_array) || (!$not_equals && $value_in_array)) {
        throw new MigrateSkipProcessException();
      }
    }
    elseif ($this->compareValue($value, $this->configuration['value'], $this->configuration['case_sensitive'], !isset($this->configuration['not_equals']))) {
      throw new MigrateSkipProcessException();
    }

    return $value;
  }

  /**
   * Compare values to see if they are equal.
   *
   * @param mixed $value
   *   Actual value.
   * @param mixed $skipValue
   *   Value to compare against.
   * @param bool $caseValue
   *   Compare as case-sensitive or case-insensitive.
   * @param bool $equal
   *   Compare as equal or not equal.
   *
   * @return bool
   *   True if the compare successfully, FALSE otherwise.
   */
  protected function compareValue($value, $skipValue, $caseValue, $equal = TRUE) {

    if ($caseValue === TRUE) {
      $value_search = strpos((string) $value, (string) $skipValue);
    }
    else {
      $value_search = stripos((string) $value, (string) $skipValue);
    }

    if ($equal) {
      if ($value_search !== FALSE) {
        return TRUE;
      }
      else {
        return $value_search;
      }
    }

    if ($value_search !== FALSE) {
      return FALSE;
    }

  }

}
