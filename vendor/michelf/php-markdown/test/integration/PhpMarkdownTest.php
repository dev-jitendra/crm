<?php

use PHPUnit\Framework\TestCase;
use Michelf\Markdown;
use Michelf\MarkdownExtra;

class PhpMarkdownTest extends TestCase
{
	
	public function dataProviderForPhpMarkdown() {
		$dir = TEST_RESOURCES_ROOT . '/php-markdown.mdtest';
		return MarkdownTestHelper::getInputOutputPaths($dir);
	}

	
	public function testTransformingOfPhpMarkdown($inputPath, $htmlPath, $xhtml = false) {
		$inputMarkdown = file_get_contents($inputPath);
		$expectedHtml = file_get_contents($htmlPath);
		$result = Markdown::defaultTransform($inputMarkdown);

		MarkdownTestHelper::assertSameNormalized(
			$expectedHtml,
			$result,
			"Markdown in $inputPath converts exactly to expected $htmlPath",
			$xhtml
		);
	}

	
	public function dataProviderForPhpMarkdownExceptEmphasis()
	{
		$dir = TEST_RESOURCES_ROOT . '/php-markdown.mdtest';
		$allTests = MarkdownTestHelper::getInputOutputPaths($dir);

		foreach ($allTests as $index => $test) {
			
			if (preg_match('~/Emphasis\.text$~', $test[0])) {
				unset($allTests[$index]);
			}
		}

		return array_values($allTests);
	}

	
	public function testPhpMarkdownMdTestWithMarkdownExtra($inputPath, $htmlPath, $xhtml = false)
	{
		$inputMarkdown = file_get_contents($inputPath);
		$expectedHtml = file_get_contents($htmlPath);

		$result = MarkdownExtra::defaultTransform($inputMarkdown);

		MarkdownTestHelper::assertSameNormalized(
			$expectedHtml,
			$result,
			"Markdown in $inputPath converts exactly to expected $htmlPath",
			$xhtml
		);
	}

	
	public function dataProviderForMarkdownExtra() {
		$dir = TEST_RESOURCES_ROOT . '/php-markdown-extra.mdtest';
		return MarkdownTestHelper::getInputOutputPaths($dir);
	}

	
	public function testTransformingOfMarkdownExtra($inputPath, $htmlPath, $xhtml = false) {
		$inputMarkdown = file_get_contents($inputPath);
		$expectedHtml = file_get_contents($htmlPath);
		$result = MarkdownExtra::defaultTransform($inputMarkdown);

		MarkdownTestHelper::assertSameNormalized(
			$expectedHtml,
			$result,
			"Markdown in $inputPath converts exactly to expected $htmlPath",
			$xhtml
		);
	}

	
	public function dataProviderForRegularMarkdown()
	{
		$dir = TEST_RESOURCES_ROOT . '/markdown.mdtest';
		return MarkdownTestHelper::getInputOutputPaths($dir);
	}

	
	public function testTransformingOfRegularMarkdown($inputPath, $htmlPath, $xhtml = false)
	{
		$inputMarkdown = file_get_contents($inputPath);
		$expectedHtml = file_get_contents($htmlPath);
		$result = Markdown::defaultTransform($inputMarkdown);

		MarkdownTestHelper::assertSameNormalized(
			$expectedHtml,
			$result,
			"Markdown in $inputPath converts exactly to expected $htmlPath",
			$xhtml
		);
	}

	
	public function testMarkdownMdTestWithMarkdownExtra($inputPath, $htmlPath, $xhtml = false)
	{
		$inputMarkdown = file_get_contents($inputPath);
		$expectedHtml = file_get_contents($htmlPath);

		$result = MarkdownExtra::defaultTransform($inputMarkdown);

		MarkdownTestHelper::assertSameNormalized(
			$expectedHtml,
			$result,
			"Markdown in $inputPath converts exactly to expected $htmlPath",
			$xhtml
		);
	}
}
