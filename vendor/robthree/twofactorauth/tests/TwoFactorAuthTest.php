<?php
require_once 'lib/TwoFactorAuth.php';
require_once 'lib/TwoFactorAuthException.php';

require_once 'lib/Providers/Qr/IQRCodeProvider.php';
require_once 'lib/Providers/Qr/BaseHTTPQRCodeProvider.php';
require_once 'lib/Providers/Qr/ImageChartsQRCodeProvider.php';
require_once 'lib/Providers/Qr/QRException.php';

require_once 'lib/Providers/Rng/IRNGProvider.php';
require_once 'lib/Providers/Rng/RNGException.php';
require_once 'lib/Providers/Rng/CSRNGProvider.php';
require_once 'lib/Providers/Rng/MCryptRNGProvider.php';
require_once 'lib/Providers/Rng/OpenSSLRNGProvider.php';
require_once 'lib/Providers/Rng/HashRNGProvider.php';
require_once 'lib/Providers/Rng/RNGException.php';

require_once 'lib/Providers/Time/ITimeProvider.php';
require_once 'lib/Providers/Time/LocalMachineTimeProvider.php';
require_once 'lib/Providers/Time/HttpTimeProvider.php';
require_once 'lib/Providers/Time/NTPTimeProvider.php';
require_once 'lib/Providers/Time/TimeException.php';

use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\IQRCodeProvider;
use RobThree\Auth\Providers\Rng\IRNGProvider;
use RobThree\Auth\Providers\Time\ITimeProvider;


class TwoFactorAuthTest extends PHPUnit_Framework_TestCase
{
    
    public function testConstructorThrowsOnInvalidDigits() {

        new TwoFactorAuth('Test', 0);
    }

    
    public function testConstructorThrowsOnInvalidPeriod() {

        new TwoFactorAuth('Test', 6, 0);
    }

    
    public function testConstructorThrowsOnInvalidAlgorithm() {

        new TwoFactorAuth('Test', 6, 30, 'xxx');
    }

    public function testGetCodeReturnsCorrectResults() {

        $tfa = new TwoFactorAuth('Test');
        $this->assertEquals('543160', $tfa->getCode('VMR466AB62ZBOKHE', 1426847216));
        $this->assertEquals('538532', $tfa->getCode('VMR466AB62ZBOKHE', 0));
    }

    
    public function testCreateSecretThrowsOnInsecureRNGProvider() {
        $rng = new TestRNGProvider();

        $tfa = new TwoFactorAuth('Test', 6, 30, 'sha1', null, $rng);
        $tfa->createSecret();
    }

    public function testCreateSecretOverrideSecureDoesNotThrowOnInsecureRNG() {
        $rng = new TestRNGProvider();

        $tfa = new TwoFactorAuth('Test', 6, 30, 'sha1', null, $rng);
        $this->assertEquals('ABCDEFGHIJKLMNOP', $tfa->createSecret(80, false));
    }

    public function testCreateSecretDoesNotThrowOnSecureRNGProvider() {
        $rng = new TestRNGProvider(true);

        $tfa = new TwoFactorAuth('Test', 6, 30, 'sha1', null, $rng);
        $this->assertEquals('ABCDEFGHIJKLMNOP', $tfa->createSecret());
    }

    public function testCreateSecretGeneratesDesiredAmountOfEntropy() {
        $rng = new TestRNGProvider(true);

        $tfa = new TwoFactorAuth('Test', 6, 30, 'sha1', null, $rng);
        $this->assertEquals('A', $tfa->createSecret(5));
        $this->assertEquals('AB', $tfa->createSecret(6));
        $this->assertEquals('ABCDEFGHIJKLMNOPQRSTUVWXYZ', $tfa->createSecret(128));
        $this->assertEquals('ABCDEFGHIJKLMNOPQRSTUVWXYZ234567', $tfa->createSecret(160));
        $this->assertEquals('ABCDEFGHIJKLMNOPQRSTUVWXYZ234567ABCDEFGHIJKLMNOPQRSTUVWXYZ234567', $tfa->createSecret(320));
        $this->assertEquals('ABCDEFGHIJKLMNOPQRSTUVWXYZ234567ABCDEFGHIJKLMNOPQRSTUVWXYZ234567A', $tfa->createSecret(321));
    }

    public function testEnsureCorrectTimeDoesNotThrowForCorrectTime() {
        $tpr1 = new TestTimeProvider(123);
        $tpr2 = new TestTimeProvider(128);

        $tfa = new TwoFactorAuth('Test', 6, 30, 'sha1', null, null, $tpr1);
        $tfa->ensureCorrectTime(array($tpr2));   
    }

    
    public function testEnsureCorrectTimeThrowsOnIncorrectTime() {
        $tpr1 = new TestTimeProvider(123);
        $tpr2 = new TestTimeProvider(124);

        $tfa = new TwoFactorAuth('Test', 6, 30, 'sha1', null, null, $tpr1);
        $tfa->ensureCorrectTime(array($tpr2), 0);    
    }


    public function testEnsureDefaultTimeProviderReturnsCorrectTime() {
        $tfa = new TwoFactorAuth('Test', 6, 30, 'sha1');
        $tfa->ensureCorrectTime(array(new TestTimeProvider(time())), 1);    
    }

    public function testEnsureAllTimeProvidersReturnCorrectTime() {
        $tfa = new TwoFactorAuth('Test', 6, 30, 'sha1');
        $tfa->ensureCorrectTime(array(
            new RobThree\Auth\Providers\Time\NTPTimeProvider(),                         
            
            new RobThree\Auth\Providers\Time\HttpTimeProvider(),                        
            new RobThree\Auth\Providers\Time\HttpTimeProvider('https:
            new RobThree\Auth\Providers\Time\HttpTimeProvider('https:
        ));
    }

    public function testVerifyCodeWorksCorrectly() {

        $tfa = new TwoFactorAuth('Test', 6, 30);
        $this->assertEquals(true , $tfa->verifyCode('VMR466AB62ZBOKHE', '543160', 1, 1426847190));
        $this->assertEquals(true , $tfa->verifyCode('VMR466AB62ZBOKHE', '543160', 0, 1426847190 + 29));	
        $this->assertEquals(false, $tfa->verifyCode('VMR466AB62ZBOKHE', '543160', 0, 1426847190 + 30));	
        $this->assertEquals(false, $tfa->verifyCode('VMR466AB62ZBOKHE', '543160', 0, 1426847190 - 1));	

        $this->assertEquals(true , $tfa->verifyCode('VMR466AB62ZBOKHE', '543160', 1, 1426847205 + 0));	
        $this->assertEquals(true , $tfa->verifyCode('VMR466AB62ZBOKHE', '543160', 1, 1426847205 + 35));	
        $this->assertEquals(true , $tfa->verifyCode('VMR466AB62ZBOKHE', '543160', 1, 1426847205 - 35));	

        $this->assertEquals(false, $tfa->verifyCode('VMR466AB62ZBOKHE', '543160', 1, 1426847205 + 65));	
        $this->assertEquals(false, $tfa->verifyCode('VMR466AB62ZBOKHE', '543160', 1, 1426847205 - 65));	

        $this->assertEquals(true , $tfa->verifyCode('VMR466AB62ZBOKHE', '543160', 2, 1426847205 + 65));	
        $this->assertEquals(true , $tfa->verifyCode('VMR466AB62ZBOKHE', '543160', 2, 1426847205 - 65));	
    }

    public function testVerifyCorrectTimeSliceIsReturned() {
        $tfa = new TwoFactorAuth('Test', 6, 30);

        
        
        $this->assertEquals(true, $tfa->verifyCode('VMR466AB62ZBOKHE', '534113', 3, 1426847190, $timeslice1));
        $this->assertEquals(47561570, $timeslice1);
        $this->assertEquals(true, $tfa->verifyCode('VMR466AB62ZBOKHE', '819652', 3, 1426847190, $timeslice2));
        $this->assertEquals(47561571, $timeslice2);
        $this->assertEquals(true, $tfa->verifyCode('VMR466AB62ZBOKHE', '915954', 3, 1426847190, $timeslice3));
        $this->assertEquals(47561572, $timeslice3);
        $this->assertEquals(true, $tfa->verifyCode('VMR466AB62ZBOKHE', '543160', 3, 1426847190, $timeslice4));
        $this->assertEquals(47561573, $timeslice4);
        $this->assertEquals(true, $tfa->verifyCode('VMR466AB62ZBOKHE', '348401', 3, 1426847190, $timeslice5));
        $this->assertEquals(47561574, $timeslice5);
        $this->assertEquals(true, $tfa->verifyCode('VMR466AB62ZBOKHE', '648525', 3, 1426847190, $timeslice6));
        $this->assertEquals(47561575, $timeslice6);
        $this->assertEquals(true, $tfa->verifyCode('VMR466AB62ZBOKHE', '170645', 3, 1426847190, $timeslice7));
        $this->assertEquals(47561576, $timeslice7);

        
        $this->assertEquals(false, $tfa->verifyCode('VMR466AB62ZBOKHE', '111111', 3, 1426847190, $timeslice8));
        $this->assertEquals(0, $timeslice8);
    }

    public function testTotpUriIsCorrect() {
        $qr = new TestQrProvider();

        $tfa = new TwoFactorAuth('Test&Issuer', 6, 30, 'sha1', $qr);
        $data = $this->DecodeDataUri($tfa->getQRCodeImageAsDataUri('Test&Label', 'VMR466AB62ZBOKHE'));
        $this->assertEquals('test/test', $data['mimetype']);
        $this->assertEquals('base64', $data['encoding']);
        $this->assertEquals('otpauth:
    }

    
    public function testGetQRCodeImageAsDataUriThrowsOnInvalidSize() {
        $qr = new TestQrProvider();

        $tfa = new TwoFactorAuth('Test', 6, 30, 'sha1', $qr);
        $tfa->getQRCodeImageAsDataUri('Test', 'VMR466AB62ZBOKHE', 0);
    }

    
    public function testGetCodeThrowsOnInvalidBase32String1() {
        $tfa = new TwoFactorAuth('Test');
        $tfa->getCode('FOO1BAR8BAZ9');    
    }

    
    public function testGetCodeThrowsOnInvalidBase32String2() {
        $tfa = new TwoFactorAuth('Test');
        $tfa->getCode('mzxw6===');        
    }

    public function testKnownBase32DecodeTestVectors() {
        
        
        
        
        
        
        

        
        
        
        $tfa = new TwoFactorAuth('Test');

        $method = new ReflectionMethod('RobThree\Auth\TwoFactorAuth', 'base32Decode');
        $method->setAccessible(true);

        
        $this->assertEquals('', $method->invoke($tfa, ''));
        $this->assertEquals('f', $method->invoke($tfa, 'MY======'));
        $this->assertEquals('fo', $method->invoke($tfa, 'MZXQ===='));
        $this->assertEquals('foo', $method->invoke($tfa, 'MZXW6==='));
        $this->assertEquals('foob', $method->invoke($tfa, 'MZXW6YQ='));
        $this->assertEquals('fooba', $method->invoke($tfa, 'MZXW6YTB'));
        $this->assertEquals('foobar', $method->invoke($tfa, 'MZXW6YTBOI======'));
    }

    public function testKnownBase32DecodeUnpaddedTestVectors() {
        
        
        
        
        $tfa = new TwoFactorAuth('Test');

        $method = new ReflectionMethod('RobThree\Auth\TwoFactorAuth', 'base32Decode');
        $method->setAccessible(true);

        
        $this->assertEquals('', $method->invoke($tfa, ''));
        $this->assertEquals('f', $method->invoke($tfa, 'MY'));
        $this->assertEquals('fo', $method->invoke($tfa, 'MZXQ'));
        $this->assertEquals('foo', $method->invoke($tfa, 'MZXW6'));
        $this->assertEquals('foob', $method->invoke($tfa, 'MZXW6YQ'));
        $this->assertEquals('fooba', $method->invoke($tfa, 'MZXW6YTB'));
        $this->assertEquals('foobar', $method->invoke($tfa, 'MZXW6YTBOI'));
    }


    public function testKnownTestVectors_sha1() {
        
        $secret = 'GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ';   
        $tfa = new TwoFactorAuth('Test', 8, 30, 'sha1');
        $this->assertEquals('94287082', $tfa->getCode($secret, 59));
        $this->assertEquals('07081804', $tfa->getCode($secret, 1111111109));
        $this->assertEquals('14050471', $tfa->getCode($secret, 1111111111));
        $this->assertEquals('89005924', $tfa->getCode($secret, 1234567890));
        $this->assertEquals('69279037', $tfa->getCode($secret, 2000000000));
        $this->assertEquals('65353130', $tfa->getCode($secret, 20000000000));
    }

    public function testKnownTestVectors_sha256() {
        
        $secret = 'GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQGEZA';   
        $tfa = new TwoFactorAuth('Test', 8, 30, 'sha256');
        $this->assertEquals('46119246', $tfa->getCode($secret, 59));
        $this->assertEquals('68084774', $tfa->getCode($secret, 1111111109));
        $this->assertEquals('67062674', $tfa->getCode($secret, 1111111111));
        $this->assertEquals('91819424', $tfa->getCode($secret, 1234567890));
        $this->assertEquals('90698825', $tfa->getCode($secret, 2000000000));
        $this->assertEquals('77737706', $tfa->getCode($secret, 20000000000));
    }

    public function testKnownTestVectors_sha512() {
        
        $secret = 'GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQGEZDGNA';   
        $tfa = new TwoFactorAuth('Test', 8, 30, 'sha512');
        $this->assertEquals('90693936', $tfa->getCode($secret, 59));
        $this->assertEquals('25091201', $tfa->getCode($secret, 1111111109));
        $this->assertEquals('99943326', $tfa->getCode($secret, 1111111111));
        $this->assertEquals('93441116', $tfa->getCode($secret, 1234567890));
        $this->assertEquals('38618901', $tfa->getCode($secret, 2000000000));
        $this->assertEquals('47863826', $tfa->getCode($secret, 20000000000));
    }

    
    public function testCSRNGProvidersReturnExpectedNumberOfBytes() {
        $rng = new \RobThree\Auth\Providers\Rng\CSRNGProvider();
        foreach ($this->getRngTestLengths() as $l)
            $this->assertEquals($l, strlen($rng->getRandomBytes($l)));
        $this->assertEquals(true, $rng->isCryptographicallySecure());
    }

    
    public function testHashRNGProvidersReturnExpectedNumberOfBytes() {
        $rng = new \RobThree\Auth\Providers\Rng\HashRNGProvider();
        foreach ($this->getRngTestLengths() as $l)
            $this->assertEquals($l, strlen($rng->getRandomBytes($l)));
        $this->assertEquals(false, $rng->isCryptographicallySecure());
    }

    
    public function testMCryptRNGProvidersReturnExpectedNumberOfBytes() {
        if (function_exists('mcrypt_create_iv')) {
            $rng = new \RobThree\Auth\Providers\Rng\MCryptRNGProvider();
            foreach ($this->getRngTestLengths() as $l)
                $this->assertEquals($l, strlen($rng->getRandomBytes($l)));
            $this->assertEquals(true, $rng->isCryptographicallySecure());
        }
    }

    
    public function testStrongOpenSSLRNGProvidersReturnExpectedNumberOfBytes() {
        $rng = new \RobThree\Auth\Providers\Rng\OpenSSLRNGProvider(true);
        foreach ($this->getRngTestLengths() as $l)
            $this->assertEquals($l, strlen($rng->getRandomBytes($l)));
        $this->assertEquals(true, $rng->isCryptographicallySecure());
    }

    
    public function testNonStrongOpenSSLRNGProvidersReturnExpectedNumberOfBytes() {
        $rng = new \RobThree\Auth\Providers\Rng\OpenSSLRNGProvider(false);
        foreach ($this->getRngTestLengths() as $l)
            $this->assertEquals($l, strlen($rng->getRandomBytes($l)));
        $this->assertEquals(false, $rng->isCryptographicallySecure());
    }


    private function getRngTestLengths() {
        return array(1, 16, 32, 256);
    }

    private function DecodeDataUri($datauri) {
        if (preg_match('/data:(?P<mimetype>[\w\.\-\/]+);(?P<encoding>\w+),(?P<data>.*)/', $datauri, $m) === 1) {
            return array(
                'mimetype' => $m['mimetype'],
                'encoding' => $m['encoding'],
                'data' => base64_decode($m['data'])
            );
        }
        return null;
    }
}

class TestRNGProvider implements IRNGProvider {
    private $isSecure;

    function __construct($isSecure = false) {
        $this->isSecure = $isSecure;
    }

    public function getRandomBytes($bytecount) {
        $result = '';
        for ($i=0; $i<$bytecount; $i++)
            $result.=chr($i);
        return $result;

    }

    public function isCryptographicallySecure() {
        return $this->isSecure;
    }
}

class TestQrProvider implements IQRCodeProvider {
    public function getQRCodeImage($qrtext, $size) {
        return $qrtext . '@' . $size;
    }

    public function getMimeType() {
        return 'test/test';
    }
}

class TestTimeProvider implements ITimeProvider {
    private $time;

    function __construct($time) {
        $this->time = $time;
    }

    public function getTime() {
        return $this->time;
    }
}