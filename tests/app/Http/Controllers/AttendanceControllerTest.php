<?php

use App\Http\Controllers\AttendanceController as Controller;
use Mockery\Mock;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Attendance;
use App\Application;
use App\User;

/**
 * Test for AttendanceController controller
 *
 * @author lucianoc
 */
class AttendanceControllerTest extends TestCase
{

    use DatabaseTransactions;

    /* @var $attendance Attendance */

    private $attendance;

    /* @var $application Application */
    private $application;

    /* @var $application User */
    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->application = factory(Application::class)->create();
        $this->attendance = factory(Attendance::class)->create([
            'application_id' => $this->application->id
        ]);
        $this->user = factory(User::class)->create();
        $this->user->applications()->save($this->application);
        $this->attendance->users()->save($this->user);
    }

    public function testAttendanceController_UserTriesToChangeStatusToPresentInAnUnexistantAttendanceEvent_RespondsError404()
    {
        $attendance = factory(Attendance::class)->create();
        $attendance_id = $attendance->id;
        $attendance->delete();
        
        $this
                ->actingAs($this->user)
                ->json('PUT', "mobile/v1/attendance/{$attendance_id}/status/present")
                ->seeStatusCode(404)
        ;
    }

    public function testAttendanceController_UserChangesStatusToPresentInAnExpiredAttendanceEvent_RespondsError403()
    {
        $attendance = $this->createExipredAttendance($this->user);
        $this
                ->actingAs($this->user)
                ->json('PUT', "mobile/v1/attendance/{$attendance->id}/status/present")
                ->seeStatusCode(403)
                ->seeJson([
                    "data" => null,
                    "error" => 10,
                    "message" => "Forbidden: Attendance event unavailable for access or modification."
                ])
        ;
    }

    public function testAttendanceController_UserChangesStatusToPresentInActiveAttendanceEvent_RespondsStatusOkAndUser()
    {
        $attendance = $this->createActiveAttendance($this->user);
        $this
                ->actingAs($this->user)
                ->json('PUT', "mobile/v1/attendance/{$attendance->id}/status/present")
                ->seeStatusCode(200)
                ->seeJson([
                    "attendance_id" => $attendance->id,
                    "user_id" => $this->user->id,
                    "status" => "p",
                ])
        ;
        $this->seeInDatabase('attendance_user', [
            'attendance_id' => $attendance->id,
            'user_id' => $this->user->id,
            'status' => 'p',
        ]);
    }

    protected function createExipredAttendance(User $attendance_user = null)
    {
        $start_time = new \DateTime();
        $start_time->sub(new DateInterval("PT3H"));

        $end_time = new \DateTime();
        $end_time->sub(new DateInterval("PT2H"));

        return $this->createAttendance(
                        $start_time,
                        $end_time,
                        $attendance_user
        );
    }

    protected function createActiveAttendance(User $attendance_user = null)
    {
        $start_time = new \DateTime();
        $start_time->sub(new DateInterval("PT1H"));

        $end_time = new \DateTime();
        $end_time->add(new DateInterval("PT1H"));

        return $this->createAttendance(
                        $start_time,
                        $end_time,
                        $attendance_user
        );
    }

    protected function createAttendance(\DateTime $start_time, \DateTime $end_time, User $attendance_user = null)
    {
        $attendance = factory(Attendance::class)->create([
            'application_id' => $this->application->id,
            'start_time' => $start_time,
            'end_time' => $end_time,
        ]);

        if ($attendance_user) {
            $attendance->users()->save($attendance_user);
        }

        return $attendance;
    }

}
