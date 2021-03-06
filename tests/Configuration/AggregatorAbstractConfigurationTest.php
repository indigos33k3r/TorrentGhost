<?php
/*
 * This file is part of TorrentGhost project.
 * You are using it at your own risk and you are fully responsible
 *  for everything that code will do.
 *
 * (c) Grzegorz Zdanowski <grzegorz@noflash.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace noFlash\TorrentGhost\Test\Configuration;

class AggregatorAbstractConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /** @var \noFlash\TorrentGhost\Configuration\AggregatorAbstractConfiguration */
    private $subjectUnderTest;

    public function setUp()
    {
        $this->subjectUnderTest = $this->getMockForAbstractClass(
            '\noFlash\TorrentGhost\Configuration\AggregatorAbstractConfiguration'
        );
    }

    public function testClassIsDefinedAbstract()
    {
        $classReflection = new \ReflectionClass('\noFlash\TorrentGhost\Configuration\AggregatorAbstractConfiguration');
        $this->assertTrue($classReflection->isAbstract());
    }

    public function testClassImplementsNameAwareConfigurationInterface()
    {
        $this->assertInstanceOf(
            '\noFlash\TorrentGhost\Configuration\NameAwareConfigurationInterface',
            $this->subjectUnderTest
        );
    }

    public function testClassUsesNamedConfigurationTrait()
    {
        $sutReflection = new \ReflectionClass('\noFlash\TorrentGhost\Configuration\AggregatorAbstractConfiguration');
        $classTraits = $sutReflection->getTraitNames();

        $this->assertContains('noFlash\TorrentGhost\Configuration\NamedConfigurationTrait', $classTraits);
    }

    public function namesProvider()
    {
        return [
            ['foo'],
            ['foobar'],
            ['foo bar'],
            ['this is long name'],
            ['special chars ü ☃'],
            ['!@#$%^&*()']
        ];
    }

    /**
     * @dataProvider namesProvider
     */
    public function testNameExtractPatternGetterProvidesDefaultPatternMatchingEverything($txt)
    {
        $pattern = $this->subjectUnderTest->getNameExtractPattern();
        $matchingResult = preg_match($pattern, $txt, $matches);

        $this->assertSame(1, $matchingResult, 'preg_match() returned invalid number of results');
        $this->assertSame([0 => $txt, 1 => $txt], $matches, 'Invalid matching result');
    }

    public function testNameExtractPatternCanBeSet()
    {
        $this->subjectUnderTest->setNameExtractPattern('/./');
        $this->assertSame('/./', $this->subjectUnderTest->getNameExtractPattern());

        $this->subjectUnderTest->setNameExtractPattern('/a/');
        $this->assertSame('/a/', $this->subjectUnderTest->getNameExtractPattern());
    }

    public function testNameExtractPatternRejectsInvalidPatterns()
    {
        $this->setExpectedException('\noFlash\TorrentGhost\Exception\RegexException');
        $this->subjectUnderTest->setNameExtractPattern('grump grump');
    }

    public function linksProvider()
    {
        return [
            ['http://example.org/file.ext'],
            ['http://example.org/file.ext?aa=b'],
            ['http://example.org/file.ext?aa=b&c=dd'],
            ['ftp://example.org/test']
        ];
    }

    /**
     * @dataProvider linksProvider
     */
    public function testLinkExtractPatternGetterProvidesDefaultPatternMatchingEverything($txt)
    {
        $pattern = $this->subjectUnderTest->getLinkExtractPattern();
        $matchingResult = preg_match($pattern, $txt, $matches);

        $this->assertSame(1, $matchingResult, 'preg_match() returned invalid number of results');
        $this->assertSame([0 => $txt, 1 => $txt], $matches, 'Invalid matching result');
    }

    public function testLinkExtractPatternCanBeSet()
    {
        $this->subjectUnderTest->setLinkExtractPattern('/./');
        $this->assertSame('/./', $this->subjectUnderTest->getLinkExtractPattern());

        $this->subjectUnderTest->setLinkExtractPattern('/a/');
        $this->assertSame('/a/', $this->subjectUnderTest->getLinkExtractPattern());
    }

    public function testLinkExtractPatternRejectsInvalidPatterns()
    {
        $this->setExpectedException('\noFlash\TorrentGhost\Exception\RegexException');
        $this->subjectUnderTest->setLinkExtractPattern('grump grump');
    }

    public function testLinkTransformPatternGetterReturnsNullByDefault()
    {
        $this->assertNull($this->subjectUnderTest->getLinkTransformPattern());
    }

    public function exampleLinkTransformPatternsProvider()
    {
        return [
            [['/^(.*?)$/', '$1?passkey=1234']],
            [['/^\[NEW\] (.*?)$/', '$1']],
            [null]
        ];
    }

    /**
     * @dataProvider exampleLinkTransformPatternsProvider
     */
    public function testLinkTransformPatternCanBeSetToCorrectValues($pattern)
    {
        $this->subjectUnderTest->setLinkTransformPattern($pattern);
        $this->assertSame($pattern, $this->subjectUnderTest->getLinkTransformPattern());
    }

    public function testLinkTransformPatternRejectsArrayWithAssocKeys()
    {
        $this->setExpectedException('\RuntimeException', 'Invalid array passed.');
        $this->subjectUnderTest->setLinkTransformPattern(['a' => '', 'b' => '']);
    }

    public function testLinkTransformPatternRejectsArrayContainingOnlySingleElement()
    {
        $this->setExpectedException('\RuntimeException', 'Invalid array passed.');
        $this->subjectUnderTest->setLinkTransformPattern(['']);
    }

    public function testLinkTransformPatternRejectsEmptyArray()
    {
        $this->setExpectedException('\RuntimeException', 'Invalid array passed.');
        $this->subjectUnderTest->setLinkTransformPattern([]);
    }

    public function testLinkTransformPatternValidatesRegexPassed()
    {
        $this->setExpectedException('\noFlash\TorrentGhost\Exception\RegexException');
        $this->subjectUnderTest->setLinkTransformPattern(['grump grump', '']);
    }

    public function testLinkCookiesGetterReturnsNullByDefault()
    {
        $this->assertNull($this->subjectUnderTest->getLinkCookies());
    }

    public function testLinkCookiesSetterAcceptsCookiesBagObjectInstance()
    {
        $cookiesBag = $this->getMockForAbstractClass('\noFlash\TorrentGhost\Http\CookiesBag');
        $this->subjectUnderTest->setLinkCookies($cookiesBag);

        $this->assertSame($cookiesBag, $this->subjectUnderTest->getLinkCookies());
    }

    public function testLinkCookiesSetterAcceptsNull()
    {
        $cookiesBag = $this->getMockForAbstractClass('\noFlash\TorrentGhost\Http\CookiesBag');
        $this->subjectUnderTest->setLinkCookies($cookiesBag);
        $this->subjectUnderTest->setLinkCookies(null);

        $this->assertNull($this->subjectUnderTest->getLinkCookies());
    }

    public function testLinkCookiesSetterRejectsObjectsOtherThanCookiesBag()
    {
        if (PHP_MAJOR_VERSION < 7) {
            /*
             * For explanation refer to links below:
             * - http://stackoverflow.com/questions/25570786/how-to-unit-test-type-hint-with-phpunit
             * - https://github.com/sebastianbergmann/phpunit/issues/178
             */
            $this->setExpectedException(get_class(new \PHPUnit_Framework_Error("", 0, "", 1)));

        } else {
            $this->setExpectedException('\TypeError');
        }

        $this->subjectUnderTest->setLinkCookies(new \stdClass());
    }

    public function testConfigurationIsConsideredValidOnFreshObject()
    {
        $this->assertTrue($this->subjectUnderTest->isValid());
    }
}
