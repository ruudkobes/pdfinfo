<?php

namespace Howtomakeaturn\PDFInfo;

/**
 * Inspired by http://stackoverflow.com/questions/14644353/get-the-number-of-pages-in-a-pdf-document/14644354
 * @author howtomakeaturn
 * @author ruudkobes
*/

class PDFInfo
{
    private $file;
    private $pdfinfoDateFormat = "D M d H:i:s Y";


    private $output;

    /** @var string */
    private $title;

    /** @var string */
    private $author;

    /** @var string */
    private $creator;

    /** @var string */
    private $producer;

    /**
     * @var \DateTime Creation date. NOTE: due to a bug in pdfinfo, the original timezone is ignored
     *
     * @todo should parse original meta information to retrieve datetime with correct timezone
     */
    private $creationDate;

    /**
     * @var \DateTime Last modification date. NOTE: due to a bug in pdfinfo, the original timezone is ignored
     *
     * @todo should parse original meta information to retrieve datetime with correct timezone
     */
    private $modDate;

    /** @var boolean */
    private $tagged;

    /** @var string */
    private $form;

    /** @var integer */
    private $pages;

    /** @var boolean */
    private $encrypted;

    /** @var string */
    private $pageSize;

    /** @var integer The filesize in bytes */
    private $fileSize;

    /** @var boolean */
    private $optimized;

    /** @var string */
    private $PDFVersion;


    /**
     * @param $file string The filename of which you want to retrieve the info
     */
    public function __construct($file)
    {
        $this->file = $file;

        $this->loadOutput();
    }
    
    private function loadOutput()
    {
        $cmd = "pdfinfo";           // Linux

        $file = escapeshellarg($this->file);
        // Parse entire output
        // Surround with double quotes if file name has spaces
        exec("$cmd $file", $output, $returnVar);

        if ( $returnVar === 1 ){
            throw new Exceptions\OpenPDFException();
        } else if ( $returnVar === 2 ){
            throw new Exceptions\OpenOutputException();
        } else if ( $returnVar === 3 ){
            throw new Exceptions\PDFPermissionException();
        } else if ( $returnVar === 99 ){
            throw new Exceptions\OtherException();
        }

        $this->output = $output;
    }

    /**
     * @param $attribute string An attribute as used in the pdfinfo output
     * @return string|null
     */
    private function parse($attribute)
    {
        // Iterate through lines
        $result = null;
        foreach($this->output as $op)
        {
            // Extract the number
            if(preg_match("/" . $attribute . ":\s*(.+)/i", $op, $matches) === 1)
            {
                $result = $matches[1];
                break;
            }
        }

        return $result;    
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        if($this->title == null)
        {
            $this->title = $this->parse('Title');
        }
        return $this->title;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        if($this->author == null)
        {
            $this->author = $this->parse('Author');
        }
        return $this->author;
    }

    /**
     * @return string
     */
    public function getCreator()
    {
        if($this->creator == null)
        {
            $this->creator = $this->parse('Creator');
        }
        return $this->creator;
    }

    /**
     * @return string
     */
    public function getProducer()
    {
        if($this->producer == null)
        {
            $this->producer = $this->parse('Producer');
        }
        return $this->producer;
    }

    /**
     * @return \DateTime
     */
    public function getCreationDate()
    {
        if($this->creationDate == null)
        {
            $this->creationDate = \DateTime::createFromFormat($this->pdfinfoDateFormat, $this->parse('CreationDate'));
        }
        return $this->creationDate;
    }

    /**
     * @return \DateTime
     */
    public function getModDate()
    {
        if($this->modDate == null)
        {
            $this->modDate = \DateTime::createFromFormat($this->pdfinfoDateFormat, $this->parse('ModDate'));
        }
        return $this->modDate;
    }

    /**
     * @return boolean
     */
    public function getTagged()
    {
        if($this->tagged == null)
        {
            $this->tagged = (strtolower($this->parse('Tagged')) === 'yes');
        }
        return $this->tagged;
    }

    /**
     * @return string
     */
    public function getForm()
    {
        if($this->form == null)
        {
            $this->form = $this->parse('Form');
        }
        return $this->form;
    }

    /**
     * @return int
     */
    public function getPages()
    {
        if($this->pages == null)
        {
            $this->pages = (int)$this->parse('Pages');
        }
        return $this->pages;
    }

    /**
     * @return boolean
     */
    public function isEncrypted()
    {
        if($this->encrypted == null)
        {
            $this->encrypted = (strtolower($this->parse('Encrypted')) === 'yes');
        }
        return $this->encrypted;
    }

    /**
     * @return string
     */
    public function getPageSize()
    {
        if($this->pageSize == null)
        {
            $this->pageSize = $this->parse('Page size');
        }
        return $this->pageSize;
    }

    /**
     * @return int
     */
    public function getFileSize()
    {
        if($this->fileSize == null)
        {
            $this->fileSize = (int)$this->parse('File size');
        }
        return $this->fileSize;
    }

    /**
     * @return boolean
     */
    public function isOptimized()
    {
        if($this->optimized == null)
        {
            $this->optimized = (strtolower($this->parse('Optimized')) === 'yes');
        }
        return $this->optimized;
    }

    /**
     * @return string
     */
    public function getPDFVersion()
    {
        if($this->PDFVersion == null)
        {
            $this->PDFVersion = $this->parse('PDF version');
        }
        return $this->PDFVersion;
    }
}