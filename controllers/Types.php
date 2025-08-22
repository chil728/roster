<?php

require_once __DIR__ . '/../helpers/SessionHelper.php';
require_once __DIR__ . '/../models/Type.php';

class Types
{
    private $typeModel;

    public function __construct()
    {
        $this->typeModel = new Type($_SESSION['userID']);
    }

    public function loadTypes()
    {
        $types = $this->typeModel->getTypesByUser();

        $_SESSION['types'] = $types;
    }

    public function addType()
    {
        $_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);

        $data = [
            'type' => trim($_POST['type']),
            'bgColor' => trim($_POST['bgColor']),
            'fontColor' => trim($_POST['fontColor'])
        ];

        if (!isset($data['type']) || !$data['type']) {
            flash('add-type-error', "Please fill in the type name", 'form-message error');
            redirect("../type");
        }

        if ($this->typeModel->isTypeExisted($data['type'])) {
            flash('add-type-error', "The type existed already", 'form-message error');
            redirect("../type");
        }

        if (!$this->typeModel->setType($data['type'], $data['bgColor'], $data['fontColor'])) {
            flash('add-type-error', "Something went wrong", 'form-message error');
            redirect("../type");
        }

        flash('add-type-success', "Type created successfully", 'form-message success');
        redirect("../home");
    }

    public function editType()
    {
        $_POST = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);

        $data = [
            'oldType' => trim($_POST['oldType']),
            'newType' => trim($_POST['newType']),
            'bgColor' => trim($_POST['bgColor']),
            'fontColor' => trim($_POST['fontColor'])
        ];

        if (!isset($data['oldType']) || !$data['oldType']) {
            flash('edit-type-error', "Please select the old type", 'form-message error');
            redirect("../type");
        }

        if (!isset($data['newType']) || !$data['newType']) {
            flash('edit-type-error', "Please fill in the new type name", 'form-message error');
            redirect("../type");
        }

        if (!$this->typeModel->updateType($data['oldType'], $data['newType'], $data['bgColor'], $data['fontColor'])) {
            flash('edit-type-error', "Something went wrong", 'form-message error');
            redirect("../type");
        }

        flash('edit-type-success', "Type edited successfully", 'form-message success');
        redirect("../home");
    }
}

$init = new Types;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    switch ($_POST['action']) {
        case 'add-type':
            $init->addType();
            break;
        case 'edit-type':
            $init->editType();
            break;
    }
}
