<?php
namespace o80;

interface Provider {

    /**
     * @param string $path The path of the directory containing the dictionaries files
     */
    public function setLangsPath($path);

    /**
     * Load the best dictionary looking at the prefered languages given in parameter.
     *
     * @param array $langs Ordered list of accepted languages, prefered ones are first
     * @return array|null The dictionary or null if not found
     * @throws CantLoadDictionaryException Thrown when there is no files in the directories path
     */
    public function load($langs);

}
