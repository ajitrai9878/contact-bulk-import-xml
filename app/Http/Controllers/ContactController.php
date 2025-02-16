<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use SimpleXMLElement;
use Exception;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $search = $request->search;
                $listData = Contact::whereNotNull('id');

                if ($search) {
                    $listData->where(function ($q) use ($search) {
                        $q->where('id', 'like', '%' . $search . '%')
                            ->orWhere('name', 'like', '%' . $search . '%')
                            ->orWhere('lastName', 'like', '%' . $search . '%')
                            ->orWhere('phone', 'like', '%' . $search . '%');
                    });
                }

                $listData = $listData->orderByDesc('id')->paginate(10);
                return view('contacts.list', compact('listData'))->render();
            }
            return view('contacts.index');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'xml_file' => 'required|file|mimes:xml|max:2048'
            ]);

            if ($validator->fails()) {
                return back()->with('error', 'Only XML files are allowed. File size must be under 2MB.');
            }

            $xmlContent = file_get_contents($request->file('xml_file')->path());
            $xml = new SimpleXMLElement($xmlContent);

            if (!isset($xml->contact)) {
                return back()->with('error', 'Invalid XML structure.');
            }

            $contactsArray = [];
            $duplicates = [];
            $existingPhones = Contact::pluck('phone')->toArray();

            foreach ($xml->contact as $contact) {
                if (!isset($contact->name, $contact->lastName, $contact->phone)) {
                    continue;
                }

                $phone = (string)$contact->phone;

                if (in_array($phone, $existingPhones)) {
                    $duplicates[] = $phone;
                } else {
                    $contactsArray[] = [
                        'name' => (string)$contact->name,
                        'lastName' => (string)$contact->lastName,
                        'phone' => $phone,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            }

            $contactModel = new Contact();
            DB::beginTransaction();
            if (!empty($contactsArray)) {
                $contactModel->bulkInsert($contactsArray);
            }
            DB::commit();

            if (!empty($contactsArray)) {
                return back()->with([
                    'success' => count($contactsArray) . ' contacts imported successfully!',
                    'duplicates' => count($duplicates) ? count($duplicates) . ' duplicate entries skipped.' : null
                ]);
            }
            return back()->with('error', 'No new contacts added. ' . count($duplicates) . ' duplicate entries found.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:20',
                'lastName' => 'required|string|max:20',
                'phone' => 'required|string|max:15|unique:contacts,phone'
            ]);

            if ($validator->fails()) {
                return ['status' => false, 'message' => $validator->errors()->first()];
            }
            $contactModel = new Contact();

            DB::beginTransaction();
            $contactModel = $contactModel->storeContact($request->all());
            if ($contactModel) {
                DB::commit();
                return ['status' => true, 'message' => 'Contact added successfully!'];
            } else {
                DB::rollBack();
                return ['status' => false, 'message' => 'Internal Server Error!'];
            }
        } catch (Exception $e) {
            DB::rollBack();
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function edit(Request $request)
    {
        try {
            $contact = Contact::find($request->id);
            if (!$contact) {
                return ['status' => false, 'message' => 'Contact not found.'];
            }
            return ['status' => true, 'data' => $contact];
        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:contacts,id',
                'name' => 'required|string|max:255',
                'lastName' => 'required|string|max:255',
                'phone' => 'required|string|max:15|unique:contacts,phone,' . $request->id
            ]);

            if ($validator->fails()) {
                return ['status' => false, 'message' => $validator->errors()->first()];
            }

            $contactModel = Contact::find($request->id);
            if (!$contactModel) {
                return ['status' => false, 'message' => 'Contact not found.'];
            }

            DB::beginTransaction();
            $contactModel = $contactModel->updateContact($request->all());
            if ($contactModel) {
                DB::commit();
                return ['status' => true, 'message' => 'Contact updated successfully!'];
            } else {
                DB::rollBack();
                return ['status' => false, 'message' => 'Internal Server Error!'];
            }

        } catch (Exception $e) {
            DB::rollBack();
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function delete(Request $request)
    {
        try {
            $contactModel = Contact::find($request->id);
            DB::beginTransaction();
            if ($contactModel) {
                $contactModel = $contactModel->deleteContact();
                if ($contactModel) {
                    DB::commit();
                    return ['status' => true, 'message' => 'Contact deleted successfully!'];
                } else {
                    DB::rollBack();
                    return ['status' => false, 'message' => 'Internal Server Error!'];
                }

            }
            return ['status' => false, 'message' => 'Contact not found!'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function bulkDelete(Request $request)
    {
        try {
            DB::beginTransaction();
            if (!empty($request->ids)) {
                $contact = Contact::whereIn('id', $request->ids)->delete();
                if ($contact) {
                    DB::commit();
                    return ['status' => true, 'message' => count($request->ids) . ' contacts deleted successfully!'];
                } else {
                    DB::rollBack();
                    return ['status' => false, 'message' => 'Internal Server Error!'];
                }
            }
            return ['status' => false, 'message' => 'No contacts selected!'];
        } catch (Exception $e) {
            DB::rollBack();
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }
}
