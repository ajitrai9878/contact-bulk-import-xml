<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static whereNotNull(string $string)
 */
class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'lastName',
        'phone'
    ];


    /**
     * Store a new contact.
     */
    public function storeContact($data)
    {
        return self::create($data);
    }

    /**
     * Update an existing contact.
     */
    public function updateContact($data)
    {
        return $this->update($data);
    }

    /**
     * Delete a contact.
     */
    public function deleteContact()
    {
        return $this->delete();
    }

    /**
     * Bulk insert contacts.
     */
    public function bulkInsert($contactsArray)
    {
        return self::insert($contactsArray);
    }
}
