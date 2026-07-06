<?php

namespace App\Controllers;

use App\Models\OrgSettingsModel;

class SettingsController extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $orgSettingsModel = new OrgSettingsModel();
        $settings = $orgSettingsModel->getSettings();

        return view('admin/settings/index', ['settings' => $settings]);
    }

    public function update()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $rules = [
            'org_name'           => 'required|min_length[2]',
            'vat_rate'           => 'required|numeric|greater_than_equal_to[0]|less_than[100]',
            'default_labor_rate' => 'required|numeric|greater_than_equal_to[0]',
            'invoice_due_days'   => 'required|integer|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'org_name'           => $this->request->getPost('org_name'),
            'org_address'        => $this->request->getPost('org_address'),
            'org_phone'          => $this->request->getPost('org_phone'),
            'org_email'          => $this->request->getPost('org_email'),
            'org_website'        => $this->request->getPost('org_website'),
            'currency'           => $this->request->getPost('currency'),
            'currency_symbol'    => $this->request->getPost('currency_symbol'),
            'vat_rate'           => $this->request->getPost('vat_rate'),
            'default_labor_rate' => $this->request->getPost('default_labor_rate'),
            'invoice_prefix'     => $this->request->getPost('invoice_prefix'),
            'invoice_due_days'   => $this->request->getPost('invoice_due_days'),
            'fin_year_start_month' => $this->request->getPost('fin_year_start_month'),
        ];

        $logo = $this->request->getFile('org_logo');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
            $extension = $logo->getExtension();
            if (!in_array($extension, $allowedTypes)) {
                return redirect()->back()->withInput()->with('error', 'Logo must be a JPG, JPEG, PNG, GIF, or SVG file.');
            }

            $uploadPath = ROOTPATH . 'uploads' . DIRECTORY_SEPARATOR . 'org';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $newName = $logo->getRandomName();
            $logo->move($uploadPath, $newName);

            $data['org_logo'] = 'uploads/org/' . $newName;
        }

        $orgSettingsModel = new OrgSettingsModel();
        $orgSettingsModel->updateSettings($data);

        return redirect()->to('/admin/settings')->with('success', 'Settings updated successfully.');
    }
}
