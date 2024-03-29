<?php
declare(strict_types=1);

namespace ZipStreamTest;

use org\bovigo\vfs\vfsStream;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use ZipStream\File;
use ZipStream\Option\Archive as ArchiveOptions;
use ZipStream\Option\File as FileOptions;
use ZipStream\Option\Method;
use ZipStream\ZipStream;


class ZipStreamTest extends TestCase
{
    const OSX_ARCHIVE_UTILITY =
        '/System/Library/CoreServices/Applications/Archive Utility.app/Contents/MacOS/Archive Utility';

    public function testFileNotFoundException(): void
    {
        $this->expectException(\ZipStream\Exception\FileNotFoundException::class);
        
        $zip = new ZipStream();

        
        $zip->addFileFromPath('foobar.php', '/foo/bar/foobar.php');
    }

    public function testFileNotReadableException(): void
    {
        
        $root = vfsStream::setup('vfs');
        
        $file = vfsStream::newFile('foo.txt', 0000)->at($root)->setContent('bar');
        $zip = new ZipStream();
        $this->expectException(\ZipStream\Exception\FileNotReadableException::class);
        $zip->addFileFromPath('foo.txt', $file->url());
    }

    public function testDostime(): void
    {
        
        $class = new \ReflectionClass(File::class);
        $method = $class->getMethod('dostime');
        $method->setAccessible(true);

        $this->assertSame($method->invoke(null, 1416246368), 1165069764);

        
        $this->assertSame($method->invoke(null, 315532800), 2162688);

        
        $this->assertSame($method->invoke(null, 0), 2162688);
    }

    public function testAddFile(): void
    {
        [$tmp, $stream] = $this->getTmpFileStream();

        $options = new ArchiveOptions();
        $options->setOutputStream($stream);

        $zip = new ZipStream(null, $options);

        $zip->addFile('sample.txt', 'Sample String Data');
        $zip->addFile('test/sample.txt', 'More Simple Sample Data');

        $zip->finish();
        fclose($stream);

        $tmpDir = $this->validateAndExtractZip($tmp);

        $files = $this->getRecursiveFileList($tmpDir);
        $this->assertEquals(['sample.txt', 'test/sample.txt'], $files);

        $this->assertStringEqualsFile($tmpDir . '/sample.txt', 'Sample String Data');
        $this->assertStringEqualsFile($tmpDir . '/test/sample.txt', 'More Simple Sample Data');
    }

    
    protected function getTmpFileStream(): array
    {
        $tmp = tempnam(sys_get_temp_dir(), 'zipstreamtest');
        $stream = fopen($tmp, 'wb+');

        return array($tmp, $stream);
    }

    
    protected function validateAndExtractZip($tmp): string
    {
        $tmpDir = $this->getTmpDir();

        $zipArch = new \ZipArchive;
        $res = $zipArch->open($tmp);

        if ($res !== true) {
            $this->fail("Failed to open {$tmp}. Code: $res");

            return $tmpDir;
        }

        $this->assertEquals(0, $zipArch->status);
        $this->assertEquals(0, $zipArch->statusSys);

        $zipArch->extractTo($tmpDir);
        $zipArch->close();

        return $tmpDir;
    }

    protected function getTmpDir(): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'zipstreamtest');
        unlink($tmp);
        mkdir($tmp) or $this->fail('Failed to make directory');

        return $tmp;
    }

    
    protected function getRecursiveFileList(string $path): array
    {
        $data = array();
        $path = (string)realpath($path);
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));

        $pathLen = strlen($path);
        foreach ($files as $file) {
            $filePath = $file->getRealPath();
            if (!is_dir($filePath)) {
                $data[] = substr($filePath, $pathLen + 1);
            }
        }

        sort($data);

        return $data;
    }

    public function testAddFileUtf8NameComment(): void
    {
        [$tmp, $stream] = $this->getTmpFileStream();

        $options = new ArchiveOptions();
        $options->setOutputStream($stream);

        $zip = new ZipStream(null, $options);

        $name = 'árvíztűrő tükörfúrógép.txt';
        $content = 'Sample String Data';
        $comment =
            'Filename has every special characters ' .
            'from Hungarian language in lowercase. ' .
            'In uppercase: ÁÍŰŐÜÖÚÓÉ';

        $fileOptions = new FileOptions();
        $fileOptions->setComment($comment);

        $zip->addFile($name, $content, $fileOptions);
        $zip->finish();
        fclose($stream);

        $tmpDir = $this->validateAndExtractZip($tmp);

        $files = $this->getRecursiveFileList($tmpDir);
        $this->assertEquals(array($name), $files);
        $this->assertStringEqualsFile($tmpDir . '/' . $name, $content);

        $zipArch = new \ZipArchive();
        $zipArch->open($tmp);
        $this->assertEquals($comment, $zipArch->getCommentName($name));
    }

    public function testAddFileUtf8NameNonUtfComment(): void
    {
        $this->expectException(\ZipStream\Exception\EncodingException::class);

        $stream = $this->getTmpFileStream()[1];

        $options = new ArchiveOptions();
        $options->setOutputStream($stream);

        $zip = new ZipStream(null, $options);

        $name = 'á.txt';
        $content = 'any';
        $comment = 'á';

        $fileOptions = new FileOptions();
        $fileOptions->setComment(mb_convert_encoding($comment, 'ISO-8859-2', 'UTF-8'));

        $zip->addFile($name, $content, $fileOptions);
    }

    public function testAddFileNonUtf8NameUtfComment(): void
    {
        $this->expectException(\ZipStream\Exception\EncodingException::class);

        $stream = $this->getTmpFileStream()[1];

        $options = new ArchiveOptions();
        $options->setOutputStream($stream);

        $zip = new ZipStream(null, $options);

        $name = 'á.txt';
        $content = 'any';
        $comment = 'á';

        $fileOptions = new FileOptions();
        $fileOptions->setComment($comment);

        $zip->addFile(mb_convert_encoding($name, 'ISO-8859-2', 'UTF-8'), $content, $fileOptions);
    }

    public function testAddFileWithStorageMethod(): void
    {
        [$tmp, $stream] = $this->getTmpFileStream();

        $options = new ArchiveOptions();
        $options->setOutputStream($stream);

        $zip = new ZipStream(null, $options);

        $fileOptions = new FileOptions();
        $fileOptions->setMethod(Method::STORE());

        $zip->addFile('sample.txt', 'Sample String Data', $fileOptions);
        $zip->addFile('test/sample.txt', 'More Simple Sample Data');
        $zip->finish();
        fclose($stream);

        $zipArch = new \ZipArchive();
        $zipArch->open($tmp);

        $sample1 = $zipArch->statName('sample.txt');
        $sample12 = $zipArch->statName('test/sample.txt');
        $this->assertEquals($sample1['comp_method'], Method::STORE);
        $this->assertEquals($sample12['comp_method'], Method::DEFLATE);

        $zipArch->close();
    }

    public function testDecompressFileWithMacUnarchiver(): void
    {
        if (!file_exists(self::OSX_ARCHIVE_UTILITY)) {
            $this->markTestSkipped('The Mac OSX Archive Utility is not available.');
        }

        [$tmp, $stream] = $this->getTmpFileStream();

        $options = new ArchiveOptions();
        $options->setOutputStream($stream);

        $zip = new ZipStream(null, $options);

        $folder = uniqid('', true);

        $zip->addFile($folder . '/sample.txt', 'Sample Data');
        $zip->finish();
        fclose($stream);

        exec(escapeshellarg(self::OSX_ARCHIVE_UTILITY) . ' ' . escapeshellarg($tmp), $output, $returnStatus);

        $this->assertEquals(0, $returnStatus);
        $this->assertCount(0, $output);

        $this->assertFileExists(dirname($tmp) . '/' . $folder . '/sample.txt');
        $this->assertStringEqualsFile(dirname($tmp) . '/' . $folder . '/sample.txt', 'Sample Data');
    }

    public function testAddFileFromPath(): void
    {
        [$tmp, $stream] = $this->getTmpFileStream();

        $options = new ArchiveOptions();
        $options->setOutputStream($stream);

        $zip = new ZipStream(null, $options);

        [$tmpExample, $streamExample] = $this->getTmpFileStream();
        fwrite($streamExample, 'Sample String Data');
        fclose($streamExample);
        $zip->addFileFromPath('sample.txt', $tmpExample);

        [$tmpExample, $streamExample] = $this->getTmpFileStream();
        fwrite($streamExample, 'More Simple Sample Data');
        fclose($streamExample);
        $zip->addFileFromPath('test/sample.txt', $tmpExample);

        $zip->finish();
        fclose($stream);

        $tmpDir = $this->validateAndExtractZip($tmp);

        $files = $this->getRecursiveFileList($tmpDir);
        $this->assertEquals(array('sample.txt', 'test/sample.txt'), $files);

        $this->assertStringEqualsFile($tmpDir . '/sample.txt', 'Sample String Data');
        $this->assertStringEqualsFile($tmpDir . '/test/sample.txt', 'More Simple Sample Data');
    }

    public function testAddFileFromPathWithStorageMethod(): void
    {
        [$tmp, $stream] = $this->getTmpFileStream();

        $options = new ArchiveOptions();
        $options->setOutputStream($stream);

        $zip = new ZipStream(null, $options);

        $fileOptions = new FileOptions();
        $fileOptions->setMethod(Method::STORE());

        [$tmpExample, $streamExample] = $this->getTmpFileStream();
        fwrite($streamExample, 'Sample String Data');
        fclose($streamExample);
        $zip->addFileFromPath('sample.txt', $tmpExample, $fileOptions);

        [$tmpExample, $streamExample] = $this->getTmpFileStream();
        fwrite($streamExample, 'More Simple Sample Data');
        fclose($streamExample);
        $zip->addFileFromPath('test/sample.txt', $tmpExample);

        $zip->finish();
        fclose($stream);

        $zipArch = new \ZipArchive();
        $zipArch->open($tmp);

        $sample1 = $zipArch->statName('sample.txt');
        $this->assertEquals(Method::STORE, $sample1['comp_method']);

        $sample2 = $zipArch->statName('test/sample.txt');
        $this->assertEquals(Method::DEFLATE, $sample2['comp_method']);

        $zipArch->close();
    }

    public function testAddLargeFileFromPath(): void
    {
        $methods = [Method::DEFLATE(), Method::STORE()];
        $falseTrue = [false, true];
        foreach ($methods as $method) {
            foreach ($falseTrue as $zeroHeader) {
                foreach ($falseTrue as $zip64) {
                    if ($zeroHeader && $method->equals(Method::DEFLATE())) {
                        continue;
                    }
                    $this->addLargeFileFileFromPath($method, $zeroHeader, $zip64);
                }
            }
        }
    }

    protected function addLargeFileFileFromPath($method, $zeroHeader, $zip64): void
    {
        [$tmp, $stream] = $this->getTmpFileStream();

        $options = new ArchiveOptions();
        $options->setOutputStream($stream);
        $options->setLargeFileMethod($method);
        $options->setLargeFileSize(5);
        $options->setZeroHeader($zeroHeader);
        $options->setEnableZip64($zip64);

        $zip = new ZipStream(null, $options);

        [$tmpExample, $streamExample] = $this->getTmpFileStream();
        for ($i = 0; $i <= 10000; $i++) {
            fwrite($streamExample, sha1((string)$i));
            if ($i % 100 === 0) {
                fwrite($streamExample, "\n");
            }
        }
        fclose($streamExample);
        $shaExample = sha1_file($tmpExample);
        $zip->addFileFromPath('sample.txt', $tmpExample);
        unlink($tmpExample);

        $zip->finish();
        fclose($stream);

        $tmpDir = $this->validateAndExtractZip($tmp);

        $files = $this->getRecursiveFileList($tmpDir);
        $this->assertEquals(array('sample.txt'), $files);

        $this->assertEquals(sha1_file($tmpDir . '/sample.txt'), $shaExample, "SHA-1 Mismatch Method: {$method}");
    }

    public function testAddFileFromStream(): void
    {
        [$tmp, $stream] = $this->getTmpFileStream();

        $options = new ArchiveOptions();
        $options->setOutputStream($stream);

        $zip = new ZipStream(null, $options);

        
        
        
        $streamExample = fopen(__FILE__, 'rb');
        $zip->addFileFromStream('sample.txt', $streamExample);


        $fileOptions = new FileOptions();
        $fileOptions->setMethod(Method::STORE());

        $streamExample2 = fopen('php:
        fwrite($streamExample2, 'More Simple Sample Data');
        rewind($streamExample2); 
        $zip->addFileFromStream('test/sample.txt', $streamExample2, $fileOptions);


        $zip->finish();
        fclose($stream);

        $tmpDir = $this->validateAndExtractZip($tmp);

        $files = $this->getRecursiveFileList($tmpDir);
        $this->assertEquals(array('sample.txt', 'test/sample.txt'), $files);

        $this->assertStringEqualsFile(__FILE__, file_get_contents($tmpDir . '/sample.txt'));
        $this->assertStringEqualsFile($tmpDir . '/test/sample.txt', 'More Simple Sample Data');
    }

    public function testAddFileFromStreamWithStorageMethod(): void
    {
        [$tmp, $stream] = $this->getTmpFileStream();

        $options = new ArchiveOptions();
        $options->setOutputStream($stream);

        $zip = new ZipStream(null, $options);

        $fileOptions = new FileOptions();
        $fileOptions->setMethod(Method::STORE());

        $streamExample = fopen('php:
        fwrite($streamExample, 'Sample String Data');
        rewind($streamExample); 
        $zip->addFileFromStream('sample.txt', $streamExample, $fileOptions);


        $streamExample2 = fopen('php:
        fwrite($streamExample2, 'More Simple Sample Data');
        rewind($streamExample2); 
        $zip->addFileFromStream('test/sample.txt', $streamExample2);


        $zip->finish();
        fclose($stream);

        $zipArch = new \ZipArchive();
        $zipArch->open($tmp);

        $sample1 = $zipArch->statName('sample.txt');
        $this->assertEquals(Method::STORE, $sample1['comp_method']);

        $sample2 = $zipArch->statName('test/sample.txt');
        $this->assertEquals(Method::DEFLATE, $sample2['comp_method']);

        $zipArch->close();
    }

    public function testAddFileFromPsr7Stream(): void
    {
        [$tmp, $stream] = $this->getTmpFileStream();

        $options = new ArchiveOptions();
        $options->setOutputStream($stream);

        $zip = new ZipStream(null, $options);

        $body = 'Sample String Data';
        $response = new Response(200, [], $body);

        $fileOptions = new FileOptions();
        $fileOptions->setMethod(Method::STORE());

        $zip->addFileFromPsr7Stream('sample.json', $response->getBody(), $fileOptions);
        $zip->finish();
        fclose($stream);

        $tmpDir = $this->validateAndExtractZip($tmp);

        $files = $this->getRecursiveFileList($tmpDir);
        $this->assertEquals(array('sample.json'), $files);
        $this->assertStringEqualsFile($tmpDir . '/sample.json', $body);
    }

    public function testAddFileFromPsr7StreamWithFileSizeSet(): void
    {
        [$tmp, $stream] = $this->getTmpFileStream();

        $options = new ArchiveOptions();
        $options->setOutputStream($stream);

        $zip = new ZipStream(null, $options);

        $body = 'Sample String Data';
        $fileSize = strlen($body);
        
        $fakePadding = "\0\0\0\0\0\0";
        $response = new Response(200, [], $body . $fakePadding);

        $fileOptions = new FileOptions();
        $fileOptions->setMethod(Method::STORE());
        $fileOptions->setSize($fileSize);
        $zip->addFileFromPsr7Stream('sample.json', $response->getBody(), $fileOptions);
        $zip->finish();
        fclose($stream);

        $tmpDir = $this->validateAndExtractZip($tmp);

        $files = $this->getRecursiveFileList($tmpDir);
        $this->assertEquals(array('sample.json'), $files);
        $this->assertStringEqualsFile($tmpDir . '/sample.json', $body);
    }

    public function testCreateArchiveWithFlushOptionSet(): void
    {
        [$tmp, $stream] = $this->getTmpFileStream();

        $options = new ArchiveOptions();
        $options->setOutputStream($stream);
        $options->setFlushOutput(true);

        $zip = new ZipStream(null, $options);

        $zip->addFile('sample.txt', 'Sample String Data');
        $zip->addFile('test/sample.txt', 'More Simple Sample Data');

        $zip->finish();
        fclose($stream);

        $tmpDir = $this->validateAndExtractZip($tmp);

        $files = $this->getRecursiveFileList($tmpDir);
        $this->assertEquals(['sample.txt', 'test/sample.txt'], $files);

        $this->assertStringEqualsFile($tmpDir . '/sample.txt', 'Sample String Data');
        $this->assertStringEqualsFile($tmpDir . '/test/sample.txt', 'More Simple Sample Data');
    }

    public function testCreateArchiveWithOutputBufferingOffAndFlushOptionSet(): void
    {
        
        ob_end_flush();
        $this->assertEquals(0, ob_get_level());

        [$tmp, $stream] = $this->getTmpFileStream();

        $options = new ArchiveOptions();
        $options->setOutputStream($stream);
        $options->setFlushOutput(true);

        $zip = new ZipStream(null, $options);

        $zip->addFile('sample.txt', 'Sample String Data');

        $zip->finish();
        fclose($stream);

        $tmpDir = $this->validateAndExtractZip($tmp);
        $this->assertStringEqualsFile($tmpDir . '/sample.txt', 'Sample String Data');

        
        ob_start();
    }
}
