<?php

declare(strict_types=1);

namespace Brick\PhoneNumber\Tests;

use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberException;
use Brick\PhoneNumber\PhoneNumberParseException;
use Brick\PhoneNumber\PhoneNumberType;
use Brick\PhoneNumber\PhoneNumberFormat;

use PHPUnit\Framework\TestCase;


class PhoneNumberTest extends TestCase
{
    private const ALPHA_NUMERIC_NUMBER = '+180074935247';
    private const AE_UAN = '+971600123456';
    private const AR_MOBILE = '+5491187654321';
    private const AR_NUMBER = '+541187654321';
    private const AU_NUMBER = '+61236618300';
    private const BS_MOBILE = '+12423570000';
    private const BS_NUMBER = '+12423651234';
    
    private const DE_NUMBER = '+4930123456';
    private const DE_SHORT_NUMBER = '+491234';
    private const GB_MOBILE = '+447912345678';
    private const GB_NUMBER = '+442070313000';
    private const IT_MOBILE = '+39345678901';
    private const IT_NUMBER = '+390236618300';
    private const JP_STAR_NUMBER = '+812345';
    
    private const MX_MOBILE1 = '+5212345678900';
    private const MX_MOBILE2 = '+5215512345678';
    private const MX_NUMBER1 = '+523312345678';
    private const MX_NUMBER2 = '+528211234567';
    private const NZ_NUMBER = '+6433316005';
    private const SG_NUMBER = '+6565218000';
    
    private const US_LONG_NUMBER = '+165025300001';
    private const US_NUMBER = '+16502530000';
    private const US_PREMIUM = '+19002530000';
    
    private const US_LOCAL_NUMBER = '+12530000';
    private const US_SHORT_BY_ONE_NUMBER = '+1650253000';
    private const US_TOLLFREE = '+18002530000';
    private const INTERNATIONAL_TOLL_FREE = '+80012345678';
    
    
    
    private const INTERNATIONAL_TOLL_FREE_TOO_LONG = '+800123456789';
    private const UNIVERSAL_PREMIUM_RATE = '+979123456789';
    private const UNKNOWN_COUNTRY_CODE_NO_RAW_INPUT = '+212345';

    
    public function testGetExampleNumber(string $regionCode, string $callingCode, ?int $numberType = null) : void
    {
        if ($numberType === null) {
            $phoneNumber = PhoneNumber::getExampleNumber($regionCode);
        } else {
            $phoneNumber = PhoneNumber::getExampleNumber($regionCode, $numberType);
        }

        self::assertInstanceOf(PhoneNumber::class, $phoneNumber);
        self::assertTrue($phoneNumber->isValidNumber());

        if ($numberType !== null) {
            self::assertSame($numberType, $phoneNumber->getNumberType());
        }

        self::assertSame($callingCode, $phoneNumber->getCountryCode());
        self::assertSame($regionCode, $phoneNumber->getRegionCode());
    }

    
    public function providerGetExampleNumber() : array
    {
        return [
            ['US', '1'],
            ['FR', '33', PhoneNumberType::FIXED_LINE],
            ['FR', '33', PhoneNumberType::MOBILE],
            ['GB', '44', PhoneNumberType::FIXED_LINE],
            ['GB', '44', PhoneNumberType::MOBILE],
        ];
    }

    public function testGetExampleNumberThrowsExceptionForInvalidRegionCode() : void
    {
        $this->expectException(PhoneNumberException::class);
        PhoneNumber::getExampleNumber('ZZ');
    }

    
    public function testGetNationalNumber(string $expectedNationalNumber, string $phoneNumber) : void
    {
        self::assertSame($expectedNationalNumber, PhoneNumber::parse($phoneNumber)->getNationalNumber());
    }

    
    public function providerGetNationalNumber() : array
    {
        return [
            ['6502530000', self::US_NUMBER],
            ['345678901', self::IT_MOBILE],
            ['236618300', self::IT_NUMBER],
            ['12345678', self::INTERNATIONAL_TOLL_FREE]
        ];
    }

    
    public function testParseNationalNumber(string $expectedNumber, string $numberToParse, string $regionCode) : void
    {
        self::assertSame($expectedNumber, (string) PhoneNumber::parse($numberToParse, $regionCode));
    }

    
    public function providerParseNationalNumber() : array
    {
        return [
            
            [self::NZ_NUMBER, '033316005', 'NZ'],
            [self::NZ_NUMBER, '33316005', 'NZ'],

            
            [self::NZ_NUMBER, '03-331 6005', 'NZ'],
            [self::NZ_NUMBER, '03 331 6005', 'NZ'],

            
            
            [self::NZ_NUMBER, '0064 3 331 6005', 'NZ'],

            
            
            [self::NZ_NUMBER, '01164 3 331 6005', 'US'],
            [self::NZ_NUMBER, '+64 3 331 6005', 'US'],




            
            [self::DE_NUMBER, '301/23456', 'DE'],

            
            


        ];
    }

    
    public function testGetRegionCode(?string $expectedRegion, string $phoneNumber) : void
    {
        self::assertSame($expectedRegion, PhoneNumber::parse($phoneNumber)->getRegionCode());
    }

    
    public function providerGetRegionCode() : array
    {
        return [
            ['BS', self::BS_NUMBER],
            ['US', self::US_NUMBER],
            ['GB', self::GB_MOBILE],
            [null, self::INTERNATIONAL_TOLL_FREE],
        ];
    }

    
    public function testGetNumberType(int $numberType, string $phoneNumber) : void
    {
        self::assertSame($numberType, PhoneNumber::parse($phoneNumber)->getNumberType());
    }

    
    public function providerGetNumberType() : array
    {
        return [
            [PhoneNumberType::PREMIUM_RATE, self::US_PREMIUM],
            [PhoneNumberType::PREMIUM_RATE, '+39892123'],
            [PhoneNumberType::PREMIUM_RATE, '+449187654321'],
            [PhoneNumberType::PREMIUM_RATE, '+499001654321'],
            [PhoneNumberType::PREMIUM_RATE, '+4990091234567'],
            [PhoneNumberType::PREMIUM_RATE, self::UNIVERSAL_PREMIUM_RATE],


            [PhoneNumberType::TOLL_FREE, '+39803123'],


            [PhoneNumberType::TOLL_FREE, '+498001234567'],
            [PhoneNumberType::TOLL_FREE, self::INTERNATIONAL_TOLL_FREE],

            [PhoneNumberType::MOBILE, self::BS_MOBILE],
            [PhoneNumberType::MOBILE, self::GB_MOBILE],










            [PhoneNumberType::FIXED_LINE, self::BS_NUMBER],
            [PhoneNumberType::FIXED_LINE, self::IT_NUMBER],
            [PhoneNumberType::FIXED_LINE, self::GB_NUMBER],
            [PhoneNumberType::FIXED_LINE, self::DE_NUMBER],

            [PhoneNumberType::FIXED_LINE_OR_MOBILE, self::US_NUMBER],





            [PhoneNumberType::VOIP, '+445631231234'],

            [PhoneNumberType::PERSONAL_NUMBER, '+447031231234'],

            [PhoneNumberType::UNKNOWN, self::US_LOCAL_NUMBER]
        ];
    }

    
    public function testIsPossibleNumber(string $phoneNumber) : void
    {
        self::assertTrue(PhoneNumber::parse($phoneNumber)->isPossibleNumber());
    }

    
    public function testIsNotPossibleNumber(string $phoneNumber) : void
    {
        self::assertFalse(PhoneNumber::parse($phoneNumber)->isPossibleNumber());
    }

    
    public function testIsValidNumber(string $phoneNumber) : void
    {
        self::assertTrue(PhoneNumber::parse($phoneNumber)->isValidNumber());
    }

    
    public function testIsNotValidNumber(string $phoneNumber) : void
    {
        self::assertFalse(PhoneNumber::parse($phoneNumber)->isValidNumber());
    }

    
    public function providerValidNumbers() : array
    {
        return [
            [self::US_NUMBER],
            [self::IT_NUMBER],
            [self::GB_MOBILE],
            [self::INTERNATIONAL_TOLL_FREE],
            [self::UNIVERSAL_PREMIUM_RATE],
            ['+6421387835']
        ];
    }

    
    public function providerPossibleButNotValidNumbers() : array
    {
        return [
            [self::US_LOCAL_NUMBER],
            ['+3923661830000'],
            ['+44791234567'],
            ['+491234'],
            ['+643316005'],
            ['+39232366']
        ];
    }

    
    public function providerNotPossibleNumbers() : array
    {
        return [
            [self::INTERNATIONAL_TOLL_FREE_TOO_LONG],
            ['+1253000']
        ];
    }

    
    public function testParseException(string $phoneNumber, ?string $regionCode = null) : void
    {
        $this->expectException(PhoneNumberParseException::class);
        PhoneNumber::parse($phoneNumber, $regionCode);
    }

    
    public function providerParseException() : array
    {
        return [
            
            [''],
            ['', 'US'],

            ['This is not a phone number', 'NZ'],
            ['1 Still not a number', 'NZ'],
            ['1 MICROSOFT', 'NZ'],
            ['12 MICROSOFT', 'NZ'],
            ['01495 72553301873 810104', 'GB'],
            ['+---', 'DE'],
            ['+***', 'DE'],
            ['+*******91', 'DE'],
            ['+ 00 210 3 331 6005', 'NZ'],

            
            ['+49 0', 'DE'],

            
            ['+02366'],
            ['+210 3456 56789', 'NZ'],

            
            ['123 456 7890'],

            
            ['123 456 7890', 'CS'],

            
            ['0044', 'GB'],
            ['0044------', 'GB'],

            
            ['011', 'US'],

            
            ['0119', 'US']
        ];
    }

    
    public function testFormatNumber(string $expected, string $phoneNumber, int $numberFormat) : void
    {
        self::assertSame($expected, PhoneNumber::parse($phoneNumber)->format($numberFormat));
    }

    
    public function providerFormatNumber() : array
    {
        return [
            
            ['(650) 253-0000', self::US_NUMBER, PhoneNumberFormat::NATIONAL],
            ['+1 650-253-0000', self::US_NUMBER, PhoneNumberFormat::INTERNATIONAL],

            ['(800) 253-0000', self::US_TOLLFREE, PhoneNumberFormat::NATIONAL],
            ['+1 800-253-0000', self::US_TOLLFREE, PhoneNumberFormat::INTERNATIONAL],

            ['(900) 253-0000', self::US_PREMIUM, PhoneNumberFormat::NATIONAL],
            ['+1 900-253-0000', self::US_PREMIUM, PhoneNumberFormat::INTERNATIONAL],

            ['tel:+1-900-253-0000', self::US_PREMIUM, PhoneNumberFormat::RFC3966],

            
            ['(242) 365-1234', self::BS_NUMBER, PhoneNumberFormat::NATIONAL],
            ['+1 242-365-1234', self::BS_NUMBER, PhoneNumberFormat::INTERNATIONAL],

            
            ['020 7031 3000', self::GB_NUMBER, PhoneNumberFormat::NATIONAL],
            ['+44 20 7031 3000', self::GB_NUMBER, PhoneNumberFormat::INTERNATIONAL],

            ['07912 345678', self::GB_MOBILE, PhoneNumberFormat::NATIONAL],
            ['+44 7912 345678', self::GB_MOBILE, PhoneNumberFormat::INTERNATIONAL],

            
            ['030 1234', '+49301234', PhoneNumberFormat::NATIONAL],
            ['+49 30 1234', '+49301234', PhoneNumberFormat::INTERNATIONAL],
            ['tel:+49-30-1234', '+49301234', PhoneNumberFormat::RFC3966],

            ['0291 123', '+49291123', PhoneNumberFormat::NATIONAL],
            ['+49 291 123', '+49291123', PhoneNumberFormat::INTERNATIONAL],

            ['0291 12345678', '+4929112345678', PhoneNumberFormat::NATIONAL],
            ['+49 291 12345678', '+4929112345678', PhoneNumberFormat::INTERNATIONAL],

            ['09123 12345', '+49912312345', PhoneNumberFormat::NATIONAL],
            ['+49 9123 12345', '+49912312345', PhoneNumberFormat::INTERNATIONAL],

            ['08021 2345', '+4980212345', PhoneNumberFormat::NATIONAL],
            ['+49 8021 2345', '+4980212345', PhoneNumberFormat::INTERNATIONAL],

            ['030 123456', self::DE_NUMBER, PhoneNumberFormat::NATIONAL],

            ['04134 1234', '+4941341234', PhoneNumberFormat::NATIONAL],

            
            ['02 3661 8300', self::IT_NUMBER, PhoneNumberFormat::NATIONAL],
            ['+39 02 3661 8300', self::IT_NUMBER, PhoneNumberFormat::INTERNATIONAL],
            ['+390236618300', self::IT_NUMBER, PhoneNumberFormat::E164],

            ['345 678 901', self::IT_MOBILE, PhoneNumberFormat::NATIONAL],
            ['+39 345 678 901', self::IT_MOBILE, PhoneNumberFormat::INTERNATIONAL],
            ['+39345678901', self::IT_MOBILE, PhoneNumberFormat::E164],

            
            ['(02) 3661 8300', self::AU_NUMBER, PhoneNumberFormat::NATIONAL],
            ['+61 2 3661 8300', self::AU_NUMBER, PhoneNumberFormat::INTERNATIONAL],
            ['+61236618300', self::AU_NUMBER, PhoneNumberFormat::E164],

            ['1800 123 456', '+611800123456', PhoneNumberFormat::NATIONAL],
            ['+61 1800 123 456', '+611800123456', PhoneNumberFormat::INTERNATIONAL],
            ['+611800123456', '+611800123456', PhoneNumberFormat::E164],

            
            ['011 8765-4321', self::AR_NUMBER, PhoneNumberFormat::NATIONAL],
            ['+54 11 8765-4321', self::AR_NUMBER, PhoneNumberFormat::INTERNATIONAL],
            ['+541187654321', self::AR_NUMBER, PhoneNumberFormat::E164],

            ['011 15-8765-4321', self::AR_MOBILE, PhoneNumberFormat::NATIONAL],
            ['+54 9 11 8765-4321', self::AR_MOBILE, PhoneNumberFormat::INTERNATIONAL],
            ['+5491187654321', self::AR_MOBILE, PhoneNumberFormat::E164],

            
















        ];
    }

    
    public function testFormatForCallingFrom(string $phoneNumber, string $countryCode, string $expected) : void
    {
        self::assertSame($expected, PhoneNumber::parse($phoneNumber)->formatForCallingFrom($countryCode));
    }

    
    public function providerFormatForCallingFrom() : array
    {
        return [
            ['+33123456789', 'FR', '01 23 45 67 89'],
            ['+33123456789', 'BE', '00 33 1 23 45 67 89'],
            ['+33123456789', 'CH', '00 33 1 23 45 67 89'],
            ['+33123456789', 'DE', '00 33 1 23 45 67 89'],
            ['+33123456789', 'GB', '00 33 1 23 45 67 89'],
            ['+33123456789', 'US', '011 33 1 23 45 67 89'],
            ['+33123456789', 'CA', '011 33 1 23 45 67 89'],
            ['+16502530000', 'US', '1 (650) 253-0000'],
            ['+16502530000', 'CA', '1 (650) 253-0000'],
            ['+16502530000', 'FR', '00 1 650-253-0000'],
            ['+16502530000', 'BE', '00 1 650-253-0000'],
            ['+16502530000', 'CH', '00 1 650-253-0000'],
            ['+16502530000', 'DE', '00 1 650-253-0000'],
            ['+16502530000', 'GB', '00 1 650-253-0000'],
        ];
    }

    
    public function testGetGeographicalAreaCode(string $phoneNumber, string $areaCode) : void
    {
        self::assertSame($areaCode, PhoneNumber::parse($phoneNumber)->getGeographicalAreaCode());
    }

    public function providerGetGeographicalAreaCode() : array
    {
        return [
            ['+442079460585', '20'],
            ['+441132224444', '113'],
            ['+447553840000', ''],
            ['+33123000000', '1'],
            ['+33234000000', '2'],
            ['+33345000000', '3'],
            ['+33456000000', '4'],
            ['+33567000000', '5'],
        ];
    }

    
    public function testIsEqualTo(string $phoneNumber1, string $phoneNumber2, bool $isEqual): void
    {
        $phoneNumber1 = PhoneNumber::parse($phoneNumber1);
        $phoneNumber2 = PhoneNumber::parse($phoneNumber2);

        self::assertSame($isEqual, $phoneNumber1->isEqualTo($phoneNumber2));
    }

    public function providerIsEqualTo(): array
    {
        return [
            ['+442079460585', '+442079460585', true],
            ['+442079460585', '+442079460586', false],
        ];
    }

    public function testJsonSerializable(): void
    {
        $data = [
            'phoneNumber' => PhoneNumber::parse('0123000000', 'FR')
        ];

        self::assertSame('{"phoneNumber":"+33123000000"}', json_encode($data));
    }

    
    public function testGetDescription(string $phoneNumber, string $locale, ?string $userRegion, ?string $expected) : void
    {
        self::assertSame($expected, PhoneNumber::parse($phoneNumber)->getDescription($locale, $userRegion));
    }

    public function providerGetDescription() : array
    {
        return [
            ['+16509036313', 'EN', null, 'Mountain View, CA'],
            ['+16509036313', 'EN', 'US', 'Mountain View, CA'],
            ['+16509036313', 'EN', 'GB', 'United States'],
            ['+16509036313', 'EN', 'FR', 'United States'],
            ['+16509036313', 'EN', 'XX', 'United States'],
            ['+16509036313', 'FR', null, 'Mountain View, CA'],
            ['+16509036313', 'FR', 'US', 'Mountain View, CA'],
            ['+16509036313', 'FR', 'GB', 'États-Unis'],
            ['+16509036313', 'FR', 'FR', 'États-Unis'],
            ['+16509036313', 'FR', 'XX', 'États-Unis'],

            ['+33381251234', 'FR', null, 'Besançon'],
            ['+33381251234', 'FR', 'FR', 'Besançon'],
            ['+33381251234', 'FR', 'US', 'France'],
            ['+33381251234', 'FR', 'XX', 'France'],
            ['+33381251234', 'EN', null, 'Besançon'],
            ['+33381251234', 'EN', 'FR', 'Besançon'],
            ['+33381251234', 'EN', 'US', 'France'],
            ['+33381251234', 'EN', 'XX', 'France'],

            ['+33328201234', 'FR', null, 'Dunkerque'],
            ['+33328201234', 'FR', 'FR', 'Dunkerque'],
            ['+33328201234', 'FR', 'US', 'France'],
            ['+33328201234', 'FR', 'XX', 'France'],
            ['+33328201234', 'GB', null, 'Dunkirk'],
            ['+33328201234', 'XX', null, 'Dunkirk'],

            ['+41229097000', 'FR', null, 'Genève'],
            ['+41229097000', 'FR', 'CH', 'Genève'],
            ['+41229097000', 'FR', 'US', 'Suisse'],
            ['+41229097000', 'XX', null, 'Geneva'],

            ['+37328000000', 'XX', null, null],
        ];
    }
}
