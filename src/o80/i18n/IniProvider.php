<?php
namespace o80\i18n;

class IniProvider implements Provider {

    private $path = '.';

    function __construct() {}

    /**
     * @param string $path The path of the directory containing the dictionaries files
     */
    public function setLangsPath($path) {
        $this->path = $path;
    }

    /**
     * Load the best dictionary looking at the prefered languages given in parameter.
     *
     * @param array $langs Ordered list of accepted languages, prefered ones are first
     * @return array|null The dictionary or null if not found
     * @throws CantLoadDictionaryException Thrown when there is no files in the directories path
     */
    public function load($langs) {
        // List file names
        $files = $this->listLangFiles();

        if (empty($files)) {
            throw new CantLoadDictionaryException(CantLoadDictionaryException::NO_DICTIONARY_FILES);
        }

        foreach ($langs as $lang) {
            $dict = $this->loadMatchingFile($files, $lang);
            if ($dict !== null) {
                return $dict;
            }
        }

        return null;
    }

    /**
     * List the files from the {@code path} directory and sort them by filename size desc.
     *
     * @return array Array of files found
     */
    public function listLangFiles() {
        $files = array_diff(scandir($this->path), array('..', '.'));
        uasort($files, function ($a, $b) {
            return strlen($a) < strlen($b);
        });
        $files = array_filter($files, function($file) {
            return substr($file, -4) === '.ini';
        });
        return $files;
    }

    /**
     * Parse a INI file from the {@code path} directry.
     *
     * @param string $filename The name of the file
     * @return array The dictionary
     */
    private function loadFile($filename) {
        return parse_ini_file($this->path . '/' . $filename);
    }

    /**
     * Load the best dictionary looking at the language code given in parameter.
     *
     * @param array $files The array of dictionary file names
     * @param string $lang The language code
     * @return array|null The dictionary found for the given language code, or null if there is no match.
     */
    private function loadMatchingFile($files, $lang) {
        // Check all file names
        foreach ($files as $file) {
            // Extract locale from filename
            $fileLocale = substr($file, 0, strlen($file) - 4);

            if (\Locale::filterMatches($lang, $fileLocale)) { // Check if filename matches $lang
                return $this->loadFile($file);
            }
        }

        return null;
    }

}