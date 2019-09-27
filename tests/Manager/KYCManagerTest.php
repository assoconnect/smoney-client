<?php

declare(strict_types=1);

namespace AssoConnect\SMoney\Tests\Manager;

use AssoConnect\SMoney\Manager\KYCManager;
use AssoConnect\SMoney\Object\KYC;
use AssoConnect\SMoney\Test\SMoneyTestCase;
use GuzzleHttp\Psr7\UploadedFile;

class KYCManagerTest extends SMoneyTestCase
{
    protected function createManager(): KYCManager
    {
        $client = $this->getClient();

        return new KYCManager($client);
    }

    public function testCreateKYCRequestRetrieveKYCRequest()
    {
        $manager = $this->createManager();

        $userPro = $this->helperCreateUser(true);

        $file1 = __DIR__ . '/../data/image2.jpg';
        $stream1 = fopen($file1, 'r+');
        $filesize1 = filesize($file1);

        $file2 = __DIR__ . '/../data/image2.png';
        $stream2 = fopen($file2, 'r+');
        $filesize2 = filesize($file2);

        $file1 = new UploadedFile($stream1, $filesize1, UPLOAD_ERR_OK, 'image2.jpg', 'image/jpeg');
        $file2 = new UploadedFile($stream2, $filesize2, UPLOAD_ERR_OK, 'image2.png', 'image/png');

        $files = [
            'address' => $file1,
            'id' => $file2,
        ];

        $kyc = $manager->submitKYCRequest($userPro->appUserId, $files);
        $this->assertSame(KYC::STATUS_PENDING, $kyc->status);

        $kycRequests = $manager->retrieveKYCRequests($userPro->appUserId);
        $this->assertEquals(1, count($kycRequests));
        $lastKyc = array_pop($kycRequests);
        $this->assertSame($kyc->id, $lastKyc->id);
    }
}
