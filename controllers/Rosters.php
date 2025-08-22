<?php
require_once __DIR__ . '/../helpers/SessionHelper.php';
require_once __DIR__ . '/../models/Roster.php';
require_once __DIR__ . '/../models/User.php';

class Rosters
{
    private $rosterModel;

    public function __construct()
    {
        $this->rosterModel = new Roster($_SESSION['userID']);
    }

    public function index()
    {
        $startDate = new DateTime('2006-12-11');
        $today = new DateTime(date('Y-m-d'));
        $interval = $startDate->diff($today);
        $days = $interval->days;
        $cycle = floor($days / 14) + 1;

        // 載入4個週期的資料（56天）
        $_SESSION['rosters'] = [];
        for ($i = 0; $i < 4; $i++) {
            $cycleRosters = $this->rosterModel->getRosterByCycle($cycle + $i);
            $_SESSION['rosters'] = array_merge($_SESSION['rosters'], $cycleRosters);
        }

        $prevRosters = $this->rosterModel->getRosterByCycle($cycle - 1);
        $nextRosters = $this->rosterModel->getRosterByCycle($cycle + 4);

        $_SESSION['prevCycle'] = count($prevRosters) == 0 && $cycle <= 1 ? 0 : 1;
        $_SESSION['nextCycle'] = count($nextRosters) == 0 ? 0 : 1;
        $_SESSION['cycle'] = $cycle;
    }

    public function getPrevRosters()
    {
        $_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);
        $prevCycle = (int) trim($_POST['cycle']);

        if ($prevCycle < 1) {
            redirect('../home');
            return;
        }

        $_SESSION['cycle'] = $prevCycle;
        // 載入4個週期的資料
        $_SESSION['rosters'] = [];
        for ($i = 0; $i < 4; $i++) {
            $cycleRosters = $this->rosterModel->getRosterByCycle($prevCycle + $i);
            $_SESSION['rosters'] = array_merge($_SESSION['rosters'], $cycleRosters);
        }

        $prevRosters = $this->rosterModel->getRosterByCycle($prevCycle - 1);
        $nextRosters = $this->rosterModel->getRosterByCycle($prevCycle + 4);

        $_SESSION['prevCycle'] = count($prevRosters) == 0 && $prevCycle <= 1 ? 0 : 1;
        $_SESSION['nextCycle'] = count($nextRosters) == 0 ? 0 : 1;

        redirect('../home');
    }

    public function getNextRosters()
    {
        $_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);
        $nextCycle = (int) trim($_POST['cycle']);

        $_SESSION['cycle'] = $nextCycle;
        // 載入4個週期的資料
        $_SESSION['rosters'] = [];
        for ($i = 0; $i < 4; $i++) {
            $cycleRosters = $this->rosterModel->getRosterByCycle($nextCycle + $i);
            $_SESSION['rosters'] = array_merge($_SESSION['rosters'], $cycleRosters);
        }

        $prevRosters = $this->rosterModel->getRosterByCycle($nextCycle - 1);
        $nextRosters = $this->rosterModel->getRosterByCycle($nextCycle + 4);

        $_SESSION['prevCycle'] = count($prevRosters) == 0 && $nextCycle <= 1 ? 0 : 1;
        $_SESSION['nextCycle'] = count($nextRosters) == 0 ? 0 : 1;

        redirect('../home');
    }

    public function addRosters()
    {
        $_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);

        $data = [
            'cycle' => (int) trim($_POST['cycle']),
            'dates' => $_POST['dates'],
            'types' => $_POST['types'],
            'remarks' => $_POST['remarks']
        ];

        $cycles = $this->rosterModel->findCycles();

        foreach ($cycles as $cycle) {
            if ((int) $cycle->cycle == $data['cycle']) {
                flash('add-roster-error', 'Cycle: ' . $cycle->cycle . ' already existed', 'form-message error');
                redirect('../add_roster');
                return;
            }
        }

        for ($i = 0; $i < 14; $i++) {
            $this->rosterModel->setRoster(
                $data['cycle'],
                $data['dates'][$i],
                $data['types'][$i],
                $data['remarks'][$i]
            );
        }

        $_SESSION['cycle'] = $data['cycle'];
        $_SESSION['rosters'] = $this->rosterModel->getRosterByCycle($data['cycle']);

        $prevRosters = $this->rosterModel->getRosterByCycle($data['cycle'] - 1);
        $nextRosters = $this->rosterModel->getRosterByCycle($data['cycle'] + 1);

        $_SESSION['prevCycle'] = count($prevRosters) == 0 && $data['cycle'] <= 1 ? 0 : 1;
        $_SESSION['nextCycle'] = count($nextRosters) == 0 ? 0 : 1;

        flash('add-roster-success', 'Rosters added successfully', 'form-message success');
        redirect('../home');
    }

    public function deleteRosters()
    {
        $_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);

        if ($this->rosterModel->deleteRosterByCycle($_POST['cycle'])) {
            $cycle = (int) trim($_POST['cycle']);
            $_SESSION['rosters'] = $this->rosterModel->getRosterByCycle($cycle - 1);
            $prevRosters = $this->rosterModel->getRosterByCycle($cycle - 2);
            $nextRosters = $this->rosterModel->getRosterByCycle($cycle);

            $_SESSION['prevCycle'] = count($prevRosters) == 0 && ($cycle - 1) <= 1 ? 0 : 1;
            $_SESSION['nextCycle'] = count($nextRosters) == 0 ? 0 : 1;
            $_SESSION['cycle'] = $cycle - 1;

            flash('delete-roster-success', 'Rosters deleted successfully', 'form-message success');
            redirect('../home');
        } else {
            flash('delete-roster-error', 'Something went wrong', 'form-message error');
            redirect('../home');
        }
    }

    public function editRosters()
    {
        $_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);

        $data = [
            'roster_id' => (int) trim($_POST['roster_id']),
            'type' => trim($_POST['type']),
            'remark' => trim($_POST['remark']),
            'cycle' => (int) trim($_POST['cycle']) // Retrieve cycle from POST data
        ];

        if ($this->rosterModel->updateRoster($data['roster_id'], $data['type'], $data['remark'])) {
            // Refresh session data for the current cycle
            $_SESSION['cycle'] = $data['cycle'];
            $_SESSION['rosters'] = $this->rosterModel->getRosterByCycle($data['cycle']);

            // Update prevCycle and nextCycle flags
            $prevRosters = $this->rosterModel->getRosterByCycle($data['cycle'] - 1);
            $nextRosters = $this->rosterModel->getRosterByCycle($data['cycle'] + 1);

            $_SESSION['prevCycle'] = count($prevRosters) == 0 && $data['cycle'] <= 1 ? 0 : 1;
            $_SESSION['nextCycle'] = count($nextRosters) == 0 ? 0 : 1;

            flash('edit-roster-success', 'Roster edited successfully', 'form-message success');
            redirect('../home');
        } else {
            flash('edit-roster-error', 'Something went wrong', 'form-message error');
            redirect('../home');
        }
    }

    public function getTodayRosters()
    {
        $startDate = new DateTime('2006-12-11');
        $today = new DateTime(date('Y-m-d'));
        $interval = $startDate->diff($today);
        $days = $interval->days;
        $todayCycle = floor($days / 14) + 1;

        $_SESSION['cycle'] = $todayCycle;
        $_SESSION['rosters'] = $this->rosterModel->getRosterByCycle($todayCycle);

        $prevRosters = $this->rosterModel->getRosterByCycle($todayCycle - 1);
        $nextRosters = $this->rosterModel->getRosterByCycle($todayCycle + 1);

        $_SESSION['prevCycle'] = count($prevRosters) == 0 && $todayCycle <= 1 ? 0 : 1;
        $_SESSION['nextCycle'] = count($nextRosters) == 0 ? 0 : 1;

        redirect('../home');
    }

    public function getRostersByID($roster_id)
    {
        return $this->rosterModel->getRostersByID($roster_id);
    }
}

$init = new Rosters;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    switch ($_POST['action']) {
        case 'addRosters':
            $init->addRosters();
            break;
        case 'deleteRosters':
            $init->deleteRosters();
            break;
        case 'getPrevRosters':
            $init->getPrevRosters();
            break;
        case 'getNextRosters':
            $init->getNextRosters();
            break;
        case 'getTodayRosters':
            $init->getTodayRosters();
            break;
        case 'editRoster':
            $init->editRosters();
            break;
        default:
            $init->index();
            break;
    }
}
