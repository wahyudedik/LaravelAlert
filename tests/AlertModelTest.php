<?php

namespace Wahyudedik\LaravelAlert\Tests;

use Orchestra\Testbench\TestCase;
use Wahyudedik\LaravelAlert\Models\Alert;

class AlertModelTest extends TestCase
{
    /** @test */
    public function it_can_create_alert_with_basic_properties()
    {
        $alert = new Alert('success', 'Test message', 'Test Title');

        $this->assertEquals('success', $alert->getType());
        $this->assertEquals('Test message', $alert->getMessage());
        $this->assertEquals('Test Title', $alert->getTitle());
        $this->assertTrue($alert->isDismissible());
        $this->assertFalse($alert->isExpired());
        $this->assertTrue($alert->isValid());
    }

    /** @test */
    public function it_can_create_alert_with_custom_options()
    {
        $options = [
            'dismissible' => false,
            'icon' => 'fas fa-check',
            'class' => 'custom-class',
            'style' => 'color: red;',
            'animation' => 'fade',
            'theme' => 'custom'
        ];

        $alert = new Alert('error', 'Test message', null, $options);

        $this->assertFalse($alert->isDismissible());
        $this->assertEquals('fas fa-check', $alert->getIcon());
        $this->assertEquals('custom-class', $alert->getClass());
        $this->assertEquals('color: red;', $alert->getStyle());
        $this->assertEquals('fade', $alert->getAnimation());
        $this->assertEquals('custom', $alert->getTheme());
    }

    /** @test */
    public function it_can_handle_expiration()
    {
        // Alert that expires in 1 second
        $alert = new Alert('info', 'Test message', null, [
            'expires_at' => time() + 1
        ]);

        $this->assertFalse($alert->isExpired());
        $this->assertTrue($alert->isValid());

        // Wait for expiration
        sleep(2);

        $this->assertTrue($alert->isExpired());
        $this->assertFalse($alert->isValid());
    }

    /** @test */
    public function it_can_handle_auto_dismiss()
    {
        $alert = new Alert('success', 'Test message', null, [
            'auto_dismiss_delay' => 5000
        ]);

        $this->assertTrue($alert->shouldAutoDismiss());
        $this->assertEquals(5000, $alert->getAutoDismissDelay());
    }

    /** @test */
    public function it_can_handle_data_attributes()
    {
        $alert = new Alert('warning', 'Test message', null, [
            'data_attributes' => [
                'custom' => 'value',
                'another' => 'test'
            ]
        ]);

        $dataAttributes = $alert->getDataAttributes();
        $this->assertEquals('value', $dataAttributes['custom']);
        $this->assertEquals('test', $dataAttributes['another']);

        $html = $alert->getDataAttributesHtml();
        $this->assertStringContains('data-custom="value"', $html);
        $this->assertStringContains('data-another="test"', $html);
    }

    /** @test */
    public function it_can_get_all_classes()
    {
        $alert = new Alert('info', 'Test message', null, [
            'class' => 'custom-class',
            'theme' => 'bootstrap',
            'animation' => 'fade'
        ]);

        $allClasses = $alert->getAllClasses();
        $this->assertStringContains('custom-class', $allClasses);
        $this->assertStringContains('alert-bootstrap', $allClasses);
        $this->assertStringContains('alert-fade', $allClasses);
    }

    /** @test */
    public function it_can_set_properties_fluently()
    {
        $alert = new Alert('success', 'Test message');

        $alert->setExpiresAt(time() + 3600)
            ->setAutoDismissDelay(3000)
            ->setAnimation('slide')
            ->setPosition('top-right')
            ->setTheme('custom')
            ->addDataAttribute('test', 'value')
            ->setHtmlContent('<strong>HTML content</strong>');

        $this->assertNotNull($alert->getExpiresAt());
        $this->assertEquals(3000, $alert->getAutoDismissDelay());
        $this->assertEquals('slide', $alert->getAnimation());
        $this->assertEquals('top-right', $alert->getPosition());
        $this->assertEquals('custom', $alert->getTheme());
        $this->assertEquals('value', $alert->getDataAttributes()['test']);
        $this->assertEquals('<strong>HTML content</strong>', $alert->getHtmlContent());
    }

    /** @test */
    public function it_can_convert_to_array()
    {
        $alert = new Alert('error', 'Test message', 'Title', [
            'dismissible' => false,
            'icon' => 'fas fa-exclamation',
            'expires_at' => time() + 3600,
            'auto_dismiss_delay' => 5000
        ]);

        $array = $alert->toArray();

        $this->assertEquals('error', $array['type']);
        $this->assertEquals('Test message', $array['message']);
        $this->assertEquals('Title', $array['title']);
        $this->assertFalse($array['dismissible']);
        $this->assertEquals('fas fa-exclamation', $array['icon']);
        $this->assertFalse($array['is_expired']);
        $this->assertTrue($array['should_auto_dismiss']);
        $this->assertTrue($array['is_valid']);
    }

    /** @test */
    public function it_can_calculate_time_until_expiration()
    {
        $alert = new Alert('info', 'Test message', null, [
            'expires_at' => time() + 3600
        ]);

        $timeLeft = $alert->getTimeUntilExpiration();
        $this->assertGreaterThan(3500, $timeLeft);
        $this->assertLessThanOrEqual(3600, $timeLeft);
    }

    /** @test */
    public function it_generates_unique_ids()
    {
        $alert1 = new Alert('success', 'Message 1');
        $alert2 = new Alert('error', 'Message 2');

        $this->assertNotEquals($alert1->getId(), $alert2->getId());
        $this->assertStringStartsWith('alert_', $alert1->getId());
        $this->assertStringStartsWith('alert_', $alert2->getId());
    }

    /** @test */
    public function it_can_handle_html_content()
    {
        $alert = new Alert('info', 'Test message', null, [
            'html_content' => '<strong>Bold text</strong> and <em>italic text</em>'
        ]);

        $this->assertEquals('<strong>Bold text</strong> and <em>italic text</em>', $alert->getHtmlContent());
    }

    /** @test */
    public function it_can_handle_position_and_theme()
    {
        $alert = new Alert('warning', 'Test message', null, [
            'position' => 'bottom-left',
            'theme' => 'tailwind'
        ]);

        $this->assertEquals('bottom-left', $alert->getPosition());
        $this->assertEquals('tailwind', $alert->getTheme());
    }
}
