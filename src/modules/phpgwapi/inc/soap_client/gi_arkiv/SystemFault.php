<?php

class SystemFault extends GeointegrasjonFault
{

    /**
     * @param string $feilKode
     */
    public function __construct($feilKode)
    {
      parent::__construct($feilKode);
    }

}
