<?php 
use PHPUnit\Framework\TestCase;

/**
*  Corresponding Class to test Dropfolder class
*
*  @author Sygnomi GmbH
*/
final class DropfolderTest extends TestCase
{
    
  /**
  * Try to upload a test file
  */
    public function testUpload() : void
    {
        $dropfolder = new Sygnomi\ScryptosApi\Dropfolder(['client' => 'Test', 'group' => 'test', 'password' => 'test']);
        $var2 = $dropfolder->upload([[
                        'name' => 'README.md',
                        'filehandler' => 'README.md'
                    ]]);
        $errors = $dropfolder->getUploadErrors();
        $this->assertEmpty($errors);
    }
  
    /**
    * Just check for upload errors
    */
    public function testErrorReturnOnEmptyFile() : void
    {
        $dropfolder = new Sygnomi\ScryptosApi\Dropfolder(['client' => 'Test', 'group' => 'test', 'password' => 'test']);
        $var2 = $dropfolder->upload([[
                        'name' => 'dateiname.test',
                        'filehandler' => 'linktolocalfile'
                    ]]);
        $errors = $dropfolder->getUploadErrors();
        $this->assertNotEmpty($errors);
    }
}
