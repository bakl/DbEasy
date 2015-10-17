<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @since: 17.10.15
 */

namespace DbEasy\Tests\Unit;


use DbEasy\Adapter\AdapterAbstract;
use DbEasy\Placeholder\PlaceholderInterface;

class Helper
{
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject | AdapterAbstract
     */
    public static function getMockCustomAdapter()
    {
        $myAdapter = \PHPUnit_Framework_MockObject_Generator::getMockForAbstractClass('\DbEasy\Adapter\AdapterAbstract');
        $myAdapter->expects(\PHPUnit_Framework_TestCase::any())
            ->method('getRegexpForIgnorePlaceholder')
            ->will(\PHPUnit_Framework_TestCase::returnValue('" (?> [^"\\\\]+|\\\\"|\\\\)* "'));

        $myAdapter->expects(\PHPUnit_Framework_TestCase::any())
            ->method('getNativeCommonPlaceholder')
            ->will(\PHPUnit_Framework_TestCase::returnValue('?'));

        return $myAdapter;
    }

    /**
     * @param $placeholderName
     * @return \PHPUnit_Framework_MockObject_MockObject | PlaceholderInterface
     */
    public static function getMockCustomPlaceholder($placeholderName)
    {
        $myPlaceholder = \PHPUnit_Framework_MockObject_Generator::getMockForAbstractClass('\DbEasy\Placeholder\PlaceholderInterface');
        $myPlaceholder->expects(\PHPUnit_Framework_TestCase::any())
            ->method('getName')
            ->will(\PHPUnit_Framework_TestCase::returnValue($placeholderName));

        $myPlaceholder->expects(\PHPUnit_Framework_TestCase::any())
            ->method('getRegexp')
            ->will(\PHPUnit_Framework_TestCase::returnValue($placeholderName));

        $myPlaceholder->expects(\PHPUnit_Framework_TestCase::any())
            ->method('transformValue')
            ->will(\PHPUnit_Framework_TestCase::returnCallback(function ($value) {
                if ($value === 'in1') return 'out1';
                if ($value === 'in2') return 'out2';
                if ($value === 'in3') return 'out3';
                if ($value === 'in4') return 'out4';
            }));

        $myPlaceholder->expects(\PHPUnit_Framework_TestCase::any())
            ->method('transformPlaceholder')
            ->will(\PHPUnit_Framework_TestCase::returnCallback(function ($value, $nativePlaceholder) {
                if (!empty($nativePlaceholder)) {
                    return $nativePlaceholder;
                }
                if ($value === 'in1') return '"out1"';
                if ($value === 'in2') return '"out2"';
                if ($value === 'in3') return '"out3"';
                if ($value === 'in4') return '"out4"';
            }));

        return $myPlaceholder;
    }
}