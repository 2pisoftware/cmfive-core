<?php

/**
 * This abstract (static) class is for defining a standard way that field types
 * can be created. The system can them look at all instances that implement
 * this interface and present them to the user. The advantage to this is that
 * modules can define their own form fields as long as it implements this
 * interface
 *
 * @author Adam Buckley <adam@2pisoftware.com>
 */
abstract class FormFieldInterface
{

    // The definition of what form types this class can manipulate
    // Format should be ["<NAME>" => "<DB VALUE>"] (note the types
    // defined here are persisted against the form object)
    protected static $_respondsTo = [
        // ["Money" => "money"]
    ];

    /**
     * The list of types that the interface responds to
     * This will be used to generate a listing of the available form
     * fields, therefore they can be anything
     *
     * @return array
     */
    public static function respondsTo()
    {
        return static::$_respondsTo;
    }

    /**
     * Returns whether or not this class can interact with a given type
     *
     * @param String $type
     * @return boolean
     */
    public static function doesRespondTo($type)
    {
        foreach (static::$_respondsTo as $respondsTo) {
            if (in_array($type, $respondsTo)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns the form element
     *
     * @param String $type
     * @return boolean
     */
    public static function formType($type)
    {
        return null;
    }

    /**
     * Returns a form for adding metadata to a field
     *
     * @param string $type
     * @return null|array
     */
    public static function metadataForm($type, Web $w)
    {
        return null;
    }

    /**
     * This is where the 'magic' happens. Based on the given type, the class
     * will modify output, the producer of these classes are entirely responsible
     * for making sure the output here is capable of dealing with errors
     *
     * The recommendation is to return the $value in the event of an error (like
     * an unknown type)
     *
     * @param FormValue $form_value
     * @param \Web $w
     * @param mixed $metadata (optional)
     * @return mixed
     */
    public static function modifyForDisplay(FormValue $form_value, $w, $metadata = null)
    {
        return $value;
    }

    /**
     * E.g. for error handling
     * public static function modifyForDisplay($type, $value) {
     *      if (!$this->doesRespondTo($type)) {
     *          return $value;
     *      }
     *
     *      // Do something to $value
     *      return $value;
     * }
     */

    /**
     * Much like the modifyForDisplay function, this function is for
     * manipulating the value, you can modify it ready for
     * persistence.
     *
     * An example of these two functions at work would be storing a datetime
     * value as a unix timestamp; in modifyForDisplay, you would convert $value
     * from a unix timestamp to a date time string (e.g 'H:i d-m-Y' format) and
     * in the modifyForPersistance function you would convert the string back to
     * a unix timestamp using strtotime()
     *
     * @see FormFieldInterface::modifyForDisplay()
     * @param FormValue $form_value
     * @return mixed
     */
    public static function modifyForPersistance(FormValue $form_value)
    {
        return $value;
    }

    /**
     * Filter form metadata matching key
     *
     * @param FormMetaData[] $metadata  metadata array to search for matching keys
     * @param String $key  key to seek matching metadata
     * @return FormMetaData|null
     */
    public static function getMetadataForKey($metadata, $key)
    {
        if (!empty($metadata)) {
            foreach ($metadata as $_meta) {
                if ($_meta->meta_key == $key) {
                    return $_meta;
                }
            }
        }
        return null;
    }

    public static function getReadableType($type)
    {
        foreach (static::$_respondsTo as $respondsTo) {
            if ($type == $respondsTo[1]) {
                return $respondsTo[0];
            }
        }

        return $type;
    }
}
