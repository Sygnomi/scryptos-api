<?php
namespace Sygnomi\ScryptosApi;

use GuzzleHttp\Client;

class Dropfolder
{
    /**
     * The full domain.
     *
     * @var string
     */
    public $domain;

    /**
     * The company name.
     *
     * @var string
     */
    public $company;

    /**
     * The group name for the upload destination.
     *
     * @var string
     */
    public $group;

    /**
     * The optional form data array for the login custom fields.
     *
     * @var array
     */
    public $form_data;

    /**
     * The urls used for logn, upload, success and logout.
     *
     * @var array
     */
    public $urls;

    /**
     * The Dropfolder password.
     *
     * @var string
     */
    protected $password;

    /**
     * The upload errors.
     *
     * @var array
     */
    public $upload_errors;


    /**
     * Create a new dropfolder instance.
     *
     * @param  $connectionData
     * @return void
     */
    public function __construct(array $connectionData)
    {
        $this->domain = isset($connectionData['domain']) ? $connectionData['domain'] : 'https://scryptos.com';
        $this->client = $connectionData['client'];
        $this->group = $connectionData['group'];
        $this->form_data = isset($connectionData['form_data']) ? $connectionData['form_data'] : [];
        $this->password = $connectionData['password'];
        $folder_link = $this->client . '/' . $this->group . '/';
        $this->urls['login'] = $this->domain . '/dropfolder/' . $folder_link;
        $this->urls['upload'] = $this->domain . '/dropfolder-plupload/' . $folder_link;
        $this->urls['success'] = $this->domain . '/dropfolder-pluploadsuccess/' . $folder_link;
        $this->urls['login'] = $this->domain . '/dropfolder-logout/' . $folder_link;
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function upload(array $files)
    {
        
        /* Convert all keys of additional form data to md5 */

        $form_data_md5 = array_map(
                function ($key) {
                    return md5($key);
                },
                array_flip($this->form_data)
            );
        $form_data_md5 = array_flip($form_data_md5);
        
        $client = new Client([
            'base_uri' => $this->domain,
            'timeout' => 2.0,
            'cookies' => true
        ]);

        /* Open login url and get cookies */
        $response = $client->request('GET', $this->urls['login']);

        if ($response->getStatusCode() != 200) {
            throw new Exception("Error Processing GET Request to " . $this->urls['login'], 1);
        }
   
        /* Login to Dropfolder */
        $response = $client->request('POST', $this->urls['login'], [
            'form_params' => [
                'password' => $this->password
            ],
        ]);

        if ($response->getStatusCode() != 200) {
            throw new Exception("Error Processing POST Request to " . $this->urls['login'], 1);
        }

        
        /* Send all custom field inputs if form_data array is not empty */
        if (!empty($this->form_data)) {
            $response = $client->request('POST', $this->urls['login'], [
                'form_params' => $form_data_md5,
            ]);

            if ($response->getStatusCode() != 200) {
                throw new Exception("Error Processing POST Request to " . $this->urls['login'], 1);
            }
        }


        /* Upload files using name and local path */
        foreach ($files as $file) {
            if (!file_exists($file['filehandler'])) {
                $this->upload_errors[] = [
                    'file_name' => $file['name'],
                    'request_status_code' => 0,
                    'request_return' => $file['filehandler'] . ' -> Source file not found!'
                ];
                continue;
            }
            $response = $client->request('POST', $this->urls['upload'], [
                'multipart' => [
                    [
                        'name' => 'name',
                        'contents' => $file['name']
                    ],
                    [
                        'name' => 'file',
                        'contents' => fopen($file['filehandler'], 'r'),
                        'filename' => $file['name']
                    ],
                    [
                        'name' => 'chunk',
                        'contents' => '0'
                    ],
                    [
                        'name' => 'chunks',
                        'contents' => '1'
                    ],
                ]
            ]);

            /* Log file upload stauts */
            if ($response->getStatusCode() != 200) {
                $this->upload_errors[] = [
                    'file_name' => $file['name'],
                    'request_status_code' => $response->getStatusCode(),
                    'request_return' => $response->getBody()
                ];
            }
        }
        
        /* Get upload success page */
        $response = $client->request('POST', $this->urls['success']);

        if ($response->getStatusCode() != 200) {
            throw new Exception("Error Processing Post Request to " . $this->urls['success'], 1);
        }

        
        // logout
        $response = $client->request('GET', $this->urls['logout']);

        if ($response->getStatusCode() != 200) {
            throw new Exception("Error Processing Get Request to " . $this->urls['logout'], 1);
        }
    }

    /**
     * Get Upload Errors.
     *
     * @return array
     */

    public function getUploadErrors()
    {
        return $this->upload_errors;
    }
}
