<?php
/**
 * This file is part of the {@link http://ontowiki.net OntoWiki} project.
 *
 * @copyright Copyright (c) 2011, {@link http://aksw.org AKSW}
 * @license http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 */

/**
 * Attachment module for the OntoWiki files extension
 *
 * @category OntoWiki
 * @package  OntoWiki_extensions_files
 * @author   {@link http://sebastian.tramp.name Sebastian Tramp}
 */
class AttachmentModule extends OntoWiki_Module
{
    /*
     * The module has two options:
     * 1. Enable the module according to the type of the selected resource or
     * 2. Enable the module without checking types (default)
     */
    private $_types = array();
    private $_useWithoutTypeCheck = true;

    /**
     * Constructor
     */
    public function init()
    {
        $config = $this->_privateConfig;

        if (isset($config->useModuleWithoutTypeCheck)) {
            $this->_useWithoutTypeCheck = (boolean)$config->useModuleWithoutTypeCheck;
        }

        if ($this->_useWithoutTypeCheck === false  && isset($config->enableForTypes)) {
            $this->_types = $config->enableForTypes->toArray();
        }

    }

    public function getTitle()
    {
        return "File Attachment";
    }

    public function shouldShow()
    {
        // show only if type matches
        return $this->_checkClass();
    }

    public function getContents()
    {
        $selectedResource = $this->_owApp->selectedResource;

        $data = array();
        $data['file_uri'] = $selectedResource;

        require_once('FilesController.php');
        $pathHashed = FilesController::getFullPath($selectedResource);
        if (is_readable($pathHashed)) {
            $data['mimeType'] = FilesController::getMimeType($selectedResource);
            return $this->render('files/moduleFile', $data);
        } else {
            return $this->render('files/moduleUpload', $data);
        }
    }

    /*
     * checks the resource types agains the configured patterns
     */
    private function _checkClass()
    {
        $resource = $this->_owApp->selectedResource;
        $rModel   = $resource->getMemoryModel();

        if ($this->_useWithoutTypeCheck === true) {
            return true;
        }

        // search with each expression using the preg matchtype
        foreach ($this->_types as $type) {
            if (isset($type['classUri'])) {
                $classUri = $type['classUri'];
            } else {
                continue;
            }
            if (
                $rModel->hasSPvalue(
                    (string) $resource,
                    EF_RDF_TYPE,
                    $classUri
                )
            ) {
                return true;
            }
        }

        // type does not match to one of the expressions
        return false;
    }
}


