[![Latest Stable Version](https://poser.pugx.org/sygnomi/scryptos-api/v/stable)](https://packagist.org/packages/sygnomi/scryptos-api)
[![Latest Unstable Version](https://poser.pugx.org/sygnomi/scryptos-api/v/unstable)](https://packagist.org/packages/sygnomi/scryptos-api)
[![License](https://poser.pugx.org/sygnomi/scryptos-api/license)](https://packagist.org/packages/sygnomi/scryptos-api)
[![composer.lock](https://poser.pugx.org/sygnomi/scryptos-api/composerlock)](https://packagist.org/packages/sygnomi/scryptos-api)

# SCRYPTOS API Library

Using the SCRYPTOS API class you can connect to your SCRYPTOS account and integrate it into your application.

# Requirements

* PHP >= 7.0
* cUrl Extension

# Installation

    composer require sygnomi/scryptos-api

# Usage

Create a new instance using your SCRYPTOS client name, group (dropfolder) and password

    $dropfolder = new Sygnomi\ScryptosApi\Dropfolder(
        ['client' => 'Test',
         'group' => 'test',
         'password' => 'test'
         'form_data' => array('field_name' => 'value') //optional
        ]
    );

Upload files into the Sharefolder

    $dropfolder->upload([
                            [
                                'name' => 'yourfilename.txt',
                                'file_path' => './local/path/To/File.txt'
                                'file_stream' => stream // Optionally you can send a file stream instead of file path
                            ]
                        ]);

Get upload errors

    $dropfolder->getUploadErrors(); //returns error array

# Features

* Upload files to Dropfolder

# Standards

* PSR-4 autoloading compliant structure
* Unit-Testing with PHPUnit

## TODO

* [ ] Add sharefolder class
