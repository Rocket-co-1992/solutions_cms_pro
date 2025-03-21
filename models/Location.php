<?php

namespace Pandao\Models;

class Location
{
    protected $pms_db;

    public $name;
    public $address;
    public $lat;
    public $lng;

    /**
     * Constructor to initialize a location with name, address, latitude, and longitude.
     *
     * @param string $name    The name of the location.
     * @param string $address The address of the location.
     * @param float  $lat     The latitude of the location.
     * @param float  $lng     The longitude of the location.
     */
    public function __construct($name, $address, $lat, $lng)
    {
        $this->name = $name;
        $this->address = $address;
        $this->lat = $lat;
        $this->lng = $lng;
    }

    /**
     * Retrieves a list of locations associated with a given page ID from the database.
     *
     * @param Database   $db      The database connection object.
     * @param int    $page_id The ID of the page to filter locations by.
     *
     * @return array An array of Location objects corresponding to the specified page ID.
     */
    public static function getLocations($db, $page_id)
    {
        $locations = [];
        $result_location = $db->query("SELECT * FROM solutionsCMS_location WHERE checked = 1 AND pages REGEXP '(^|,)" . $page_id . "(,|$)'");

        if ($result_location !== false) {
            foreach ($result_location as $row) {
                $locations[] = new self(
                    addslashes($row['name']),
                    addslashes($row['address']),
                    $row['lat'],
                    $row['lng']
                );
            }
        }
        return $locations;
    }

    /**
     * Converts the location object to an associative array.
     *
     * @return array An associative array representing the location.
     */
    public function toArray()
    {
        return [
            'name' => $this->name,
            'address' => $this->address,
            'lat' => $this->lat,
            'lng' => $this->lng,
        ];
    }
}
