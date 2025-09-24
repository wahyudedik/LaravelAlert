<?php

namespace Tests\Performance;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Wahyudedik\LaravelAlert\Facades\Alert;
use Wahyudedik\LaravelAlert\Managers\AlertManager;
use Wahyudedik\LaravelAlert\Managers\PerformanceOptimizer;

class AlertPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected AlertManager $alertManager;
    protected PerformanceOptimizer $performanceOptimizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->alertManager = new AlertManager();
        $this->performanceOptimizer = new PerformanceOptimizer();
    }

    /** @test */
    public function it_can_handle_large_number_of_alerts_efficiently()
    {
        $startTime = microtime(true);

        // Create 1000 alerts
        for ($i = 0; $i < 1000; $i++) {
            Alert::success("Alert {$i}");
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(5.0, $executionTime, 'Creating 1000 alerts should take less than 5 seconds');
        $this->assertEquals(1000, Alert::count());
    }

    /** @test */
    public function it_can_handle_alert_creation_with_optimization()
    {
        $startTime = microtime(true);

        // Enable performance optimization
        $this->performanceOptimizer->enableBatchProcessing();
        $this->performanceOptimizer->enableLazyLoading();
        $this->performanceOptimizer->enableQueryOptimization();

        // Create 500 alerts with optimization
        for ($i = 0; $i < 500; $i++) {
            Alert::success("Optimized Alert {$i}");
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(2.0, $executionTime, 'Creating 500 optimized alerts should take less than 2 seconds');
        $this->assertEquals(500, Alert::count());
    }

    /** @test */
    public function it_can_handle_alert_retrieval_efficiently()
    {
        // Create 100 alerts
        for ($i = 0; $i < 100; $i++) {
            Alert::success("Alert {$i}");
        }

        $startTime = microtime(true);

        // Retrieve all alerts
        $alerts = Alert::getAlerts();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(0.1, $executionTime, 'Retrieving 100 alerts should take less than 0.1 seconds');
        $this->assertCount(100, $alerts);
    }

    /** @test */
    public function it_can_handle_alert_filtering_efficiently()
    {
        // Create mixed alerts
        for ($i = 0; $i < 100; $i++) {
            Alert::success("Success Alert {$i}");
            Alert::error("Error Alert {$i}");
            Alert::warning("Warning Alert {$i}");
            Alert::info("Info Alert {$i}");
        }

        $startTime = microtime(true);

        // Filter by type
        $successAlerts = Alert::getAlertsByType('success');
        $errorAlerts = Alert::getAlertsByType('error');
        $warningAlerts = Alert::getAlertsByType('warning');
        $infoAlerts = Alert::getAlertsByType('info');

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(0.2, $executionTime, 'Filtering 400 alerts should take less than 0.2 seconds');
        $this->assertCount(100, $successAlerts);
        $this->assertCount(100, $errorAlerts);
        $this->assertCount(100, $warningAlerts);
        $this->assertCount(100, $infoAlerts);
    }

    /** @test */
    public function it_can_handle_alert_clearing_efficiently()
    {
        // Create 500 alerts
        for ($i = 0; $i < 500; $i++) {
            Alert::success("Alert {$i}");
        }

        $startTime = microtime(true);

        // Clear all alerts
        Alert::clear();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(0.5, $executionTime, 'Clearing 500 alerts should take less than 0.5 seconds');
        $this->assertEquals(0, Alert::count());
    }

    /** @test */
    public function it_can_handle_alert_cleanup_efficiently()
    {
        // Create alerts with expiration
        for ($i = 0; $i < 200; $i++) {
            Alert::addWithExpiration('info', "Expiring Alert {$i}", null, -3600); // Expired
        }

        for ($i = 0; $i < 100; $i++) {
            Alert::success("Valid Alert {$i}");
        }

        $startTime = microtime(true);

        // Cleanup expired alerts
        Alert::cleanupExpired();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(0.3, $executionTime, 'Cleaning up 200 expired alerts should take less than 0.3 seconds');
        $this->assertEquals(100, Alert::count());
    }

    /** @test */
    public function it_can_handle_alert_bulk_operations_efficiently()
    {
        $startTime = microtime(true);

        // Create bulk alerts
        $alerts = [];
        for ($i = 0; $i < 100; $i++) {
            $alerts[] = [
                'type' => 'success',
                'message' => "Bulk Alert {$i}",
                'title' => "Bulk Title {$i}"
            ];
        }

        Alert::addMultiple($alerts);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, 'Creating 100 bulk alerts should take less than 1 second');
        $this->assertEquals(100, Alert::count());
    }

    /** @test */
    public function it_can_handle_alert_rendering_efficiently()
    {
        // Create 50 alerts
        for ($i = 0; $i < 50; $i++) {
            Alert::success("Alert {$i}");
        }

        $startTime = microtime(true);

        // Render all alerts
        $html = Alert::renderAll();

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(0.5, $executionTime, 'Rendering 50 alerts should take less than 0.5 seconds');
        $this->assertStringContainsString('Alert 0', $html);
        $this->assertStringContainsString('Alert 49', $html);
    }

    /** @test */
    public function it_can_handle_alert_memory_usage_efficiently()
    {
        $initialMemory = memory_get_usage();

        // Create 1000 alerts
        for ($i = 0; $i < 1000; $i++) {
            Alert::success("Alert {$i}");
        }

        $peakMemory = memory_get_peak_usage();
        $memoryUsed = $peakMemory - $initialMemory;

        // Memory usage should be reasonable (less than 10MB for 1000 alerts)
        $this->assertLessThan(10 * 1024 * 1024, $memoryUsed, 'Memory usage for 1000 alerts should be less than 10MB');
    }

    /** @test */
    public function it_can_handle_alert_concurrent_operations()
    {
        $startTime = microtime(true);

        // Simulate concurrent operations
        $processes = [];
        for ($i = 0; $i < 10; $i++) {
            $processes[] = function () use ($i) {
                for ($j = 0; $j < 10; $j++) {
                    Alert::success("Process {$i} Alert {$j}");
                }
            };
        }

        // Execute all processes
        foreach ($processes as $process) {
            $process();
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(2.0, $executionTime, 'Concurrent operations should complete in less than 2 seconds');
        $this->assertEquals(100, Alert::count());
    }

    /** @test */
    public function it_can_handle_alert_database_operations_efficiently()
    {
        // Enable database storage
        config(['laravel-alert.storage.driver' => 'database']);

        $startTime = microtime(true);

        // Create 100 alerts with database storage
        for ($i = 0; $i < 100; $i++) {
            Alert::success("Database Alert {$i}");
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(3.0, $executionTime, 'Creating 100 database alerts should take less than 3 seconds');
        $this->assertEquals(100, Alert::count());
    }

    /** @test */
    public function it_can_handle_alert_cache_operations_efficiently()
    {
        // Enable cache storage
        config(['laravel-alert.storage.driver' => 'cache']);

        $startTime = microtime(true);

        // Create 100 alerts with cache storage
        for ($i = 0; $i < 100; $i++) {
            Alert::success("Cache Alert {$i}");
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, 'Creating 100 cache alerts should take less than 1 second');
        $this->assertEquals(100, Alert::count());
    }

    /** @test */
    public function it_can_handle_alert_redis_operations_efficiently()
    {
        // Enable Redis storage
        config(['laravel-alert.storage.driver' => 'redis']);

        $startTime = microtime(true);

        // Create 100 alerts with Redis storage
        for ($i = 0; $i < 100; $i++) {
            Alert::success("Redis Alert {$i}");
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.5, $executionTime, 'Creating 100 Redis alerts should take less than 1.5 seconds');
        $this->assertEquals(100, Alert::count());
    }

    /** @test */
    public function it_can_handle_alert_session_operations_efficiently()
    {
        // Enable session storage
        config(['laravel-alert.storage.driver' => 'session']);

        $startTime = microtime(true);

        // Create 100 alerts with session storage
        for ($i = 0; $i < 100; $i++) {
            Alert::success("Session Alert {$i}");
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(0.5, $executionTime, 'Creating 100 session alerts should take less than 0.5 seconds');
        $this->assertEquals(100, Alert::count());
    }

    /** @test */
    public function it_can_handle_alert_ajax_operations_efficiently()
    {
        $startTime = microtime(true);

        // Simulate AJAX operations
        for ($i = 0; $i < 50; $i++) {
            // Simulate AJAX request
            $response = $this->post('/api/alerts', [
                'type' => 'success',
                'message' => "AJAX Alert {$i}",
                'title' => "AJAX Title {$i}"
            ]);

            $response->assertStatus(201);
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(5.0, $executionTime, '50 AJAX operations should complete in less than 5 seconds');
    }

    /** @test */
    public function it_can_handle_alert_websocket_operations_efficiently()
    {
        // Enable WebSocket integration
        config(['laravel-alert.websocket.enabled' => true]);

        $startTime = microtime(true);

        // Simulate WebSocket operations
        for ($i = 0; $i < 50; $i++) {
            Alert::success("WebSocket Alert {$i}");
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(2.0, $executionTime, '50 WebSocket operations should complete in less than 2 seconds');
        $this->assertEquals(50, Alert::count());
    }

    /** @test */
    public function it_can_handle_alert_pusher_operations_efficiently()
    {
        // Enable Pusher integration
        config(['laravel-alert.pusher.enabled' => true]);

        $startTime = microtime(true);

        // Simulate Pusher operations
        for ($i = 0; $i < 50; $i++) {
            Alert::success("Pusher Alert {$i}");
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(2.0, $executionTime, '50 Pusher operations should complete in less than 2 seconds');
        $this->assertEquals(50, Alert::count());
    }

    /** @test */
    public function it_can_handle_alert_email_operations_efficiently()
    {
        // Enable email integration
        config(['laravel-alert.email.enabled' => true]);

        $startTime = microtime(true);

        // Simulate email operations
        for ($i = 0; $i < 20; $i++) {
            Alert::success("Email Alert {$i}");
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(3.0, $executionTime, '20 email operations should complete in less than 3 seconds');
        $this->assertEquals(20, Alert::count());
    }

    /** @test */
    public function it_can_handle_alert_performance_optimization()
    {
        $startTime = microtime(true);

        // Enable all performance optimizations
        $this->performanceOptimizer->enableBatchProcessing();
        $this->performanceOptimizer->enableLazyLoading();
        $this->performanceOptimizer->enableQueryOptimization();
        $this->performanceOptimizer->enableMemoryOptimization();
        $this->performanceOptimizer->enableConnectionPooling();
        $this->performanceOptimizer->enableCompression();

        // Create 200 alerts with optimization
        for ($i = 0; $i < 200; $i++) {
            Alert::success("Optimized Alert {$i}");
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(1.0, $executionTime, '200 optimized alerts should complete in less than 1 second');
        $this->assertEquals(200, Alert::count());
    }

    /** @test */
    public function it_can_handle_alert_stress_testing()
    {
        $startTime = microtime(true);

        // Stress test with 5000 alerts
        for ($i = 0; $i < 5000; $i++) {
            Alert::success("Stress Test Alert {$i}");
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(10.0, $executionTime, '5000 alerts should complete in less than 10 seconds');
        $this->assertEquals(5000, Alert::count());

        // Test retrieval
        $retrieveStartTime = microtime(true);
        $alerts = Alert::getAlerts();
        $retrieveEndTime = microtime(true);
        $retrieveExecutionTime = $retrieveEndTime - $retrieveStartTime;

        $this->assertLessThan(1.0, $retrieveExecutionTime, 'Retrieving 5000 alerts should take less than 1 second');
        $this->assertCount(5000, $alerts);
    }

    /** @test */
    public function it_can_handle_alert_memory_stress_testing()
    {
        $initialMemory = memory_get_usage();

        // Create 10000 alerts for memory stress test
        for ($i = 0; $i < 10000; $i++) {
            Alert::success("Memory Stress Alert {$i}");
        }

        $peakMemory = memory_get_peak_usage();
        $memoryUsed = $peakMemory - $initialMemory;

        // Memory usage should be reasonable (less than 50MB for 10000 alerts)
        $this->assertLessThan(50 * 1024 * 1024, $memoryUsed, 'Memory usage for 10000 alerts should be less than 50MB');
        $this->assertEquals(10000, Alert::count());
    }

    /** @test */
    public function it_can_handle_alert_concurrent_stress_testing()
    {
        $startTime = microtime(true);

        // Simulate 100 concurrent processes
        $processes = [];
        for ($i = 0; $i < 100; $i++) {
            $processes[] = function () use ($i) {
                for ($j = 0; $j < 10; $j++) {
                    Alert::success("Concurrent Process {$i} Alert {$j}");
                }
            };
        }

        // Execute all processes concurrently
        foreach ($processes as $process) {
            $process();
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->assertLessThan(5.0, $executionTime, '100 concurrent processes should complete in less than 5 seconds');
        $this->assertEquals(1000, Alert::count());
    }
}
