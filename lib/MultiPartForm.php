<?php
namespace Fpp;

require_once 'MimeTypes.php';

/**
 * The multipart constructure class.
 */
class MultiPartForm
{
    private $forms = array();

    private $files = array();

    private $boundary = '';

    /**
     * The constructor of the class
     * @param array $forms The multi part forms, for example: $form = array(['api_key', 'abc'], ['api_secret', '123'])
     * @param array $files The multi part files, for example: $form = array(['image_file', 'sample.jpg', 'image/jpeg', $content])
     * @return The Refference of the instance
     */
    public function __construct($forms=null, $files=null)
    {
        if (!empty($forms)) {
            $this->forms = $forms;
        }

        if (!empty($files)) {
            $this->files = $files;
        }

        $this->boundary = $this->chooseBoundary();
    }


    /**
     * rand character
    * @access public
    * @param integer $length
    * @param string $specialChars 是否有特殊字符
    * @return string
    */
    public function chooseBoundary()
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        $result = '';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < 15; $i++) {
            $result .= $chars[rand(0, $max)];
        }

        $boundary = sprintf('%s%s%s', '------', 'PhplibFormBoundary', $result);

        return $boundary;
    }

    /**
     * Gets the multipart content type
     */
    public function getContentType()
    {
        return sprintf('multipart/form-data; boundary=%s', $this->boundary);
    }

    /**
     * @param string $name the post request field name
     * @param string $value the request field value
     */
    public function addForm($name, $value)
    {
        array_push($this->forms, [$name, $value]);
    }

    /**
     * Add multi forms one time
     * @param array $forms
     */
    public function addForms($forms) {
        foreach ($forms as $key => $value) {
            array_push($this->forms, [$key, $value]);
        }
    }

    /**
     * @param string $field the file part field name
     * @param string $name the file name
     * @param string $content the file content
     * @param string $mimetype the file type, for example image/jpeg, etc
     */
    public function addFile($field, $name, $content, $mimetype=NULL)
    {
        if(empty($mimetype)) {
            $mimetype = MimeTypes::getMimetype($name);
        }
        array_push($this->files, [$field, $name, $mimetype, $content]);
    }

    /**
     * return the MultiPartForm string body
     */
    public function __toString()
    {
        $parts = array();
        $part_boundary = "--" . $this->boundary;

        foreach ($this->forms as list($key, $val)) {
            $one = [$part_boundary, sprintf('Content-Disposition: form-data; name="%s"', $key), '', $val];
            array_push($parts, $one);
        }

        foreach ($this->files as list($field, $name, $mimetype, $content)) {
            $one = [$part_boundary, sprintf('Content-Disposition: file; name="%s"; filename="%s"', $field, $name),
                    sprintf('Content-Type: %s', $mimetype), '', $content];
            array_push($parts, $one);
        }

        // Flatten the list and add closing boundary marker,
        // then return CR+LF separated data
        $parts = array_map(function($val) {return join($val, "\r\n");}, $parts);
        $end_boundary = $part_boundary . "--";
        array_push($parts, $end_boundary);

        return join($parts, "\r\n");
    }
}
?>
