<?php

/*
  +---------------------------------------------------------------------------------+
  | Copyright (c) 2013 César Rodas                                                  |
  +---------------------------------------------------------------------------------+
  | Redistribution and use in source and binary forms, with or without              |
  | modification, are permitted provided that the following conditions are met:     |
  | 1. Redistributions of source code must retain the above copyright               |
  |    notice, this list of conditions and the following disclaimer.                |
  |                                                                                 |
  | 2. Redistributions in binary form must reproduce the above copyright            |
  |    notice, this list of conditions and the following disclaimer in the          |
  |    documentation and/or other materials provided with the distribution.         |
  |                                                                                 |
  | 3. All advertising materials mentioning features or use of this software        |
  |    must display the following acknowledgement:                                  |
  |    This product includes software developed by César D. Rodas.                  |
  |                                                                                 |
  | 4. Neither the name of the César D. Rodas nor the                               |
  |    names of its contributors may be used to endorse or promote products         |
  |    derived from this software without specific prior written permission.        |
  |                                                                                 |
  | THIS SOFTWARE IS PROVIDED BY CÉSAR D. RODAS ''AS IS'' AND ANY                   |
  | EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED       |
  | WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE          |
  | DISCLAIMED. IN NO EVENT SHALL CÉSAR D. RODAS BE LIABLE FOR ANY                  |
  | DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES      |
  | (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;    |
  | LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND     |
  | ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT      |
  | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS   |
  | SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE                     |
  +---------------------------------------------------------------------------------+
  | Authors: César Rodas <crodas@php.net>                                           |
  +---------------------------------------------------------------------------------+
 */

namespace crodas\InfluxPHP;

use ArrayIterator;
use ArrayObject;

class Cursor extends ArrayIterator
{

    /**
     * Constructor, create cursor, i.e. array of results
     * 
     * @param array $resultset
     * @return type
     */
    public function __construct(array $resultset)
    {
        $rows = array();
        if (!isset($resultset['results'][0]['series'][0])) {
            return null;
        }



//var_dump($resultset);
        if (isset($resultset['results'][0]['series'])) {
            foreach ($resultset['results'][0]['series'] as $resultElem) {
                //var_dump($resultElem);
                $row = $this->createResultSeriesObject($resultElem);
                var_dump($row);    
            }
        }
        die;
        $ao = new \ArrayObject($resultset);
        var_dump($ao['results']);
        var_dump($ao->count());
        die;
        // maybe todo: get meta information like tags and name out of resultset
        $resultColumns = $resultset['results'][0]['series'][0]['columns'];
        $resultValues = $resultset['results'][0]['series'][0]['values'];
        foreach ($resultValues as $row) {
            if (count($resultColumns) != count($row)) {
                $diffCount = abs(count($resultColumns) - count($row));
                $resultColumns = array_pad($resultColumns, count($row), null);
                $row = array_pad($row, count($resultColumns), null);
            }

            $row = (object) array_combine($resultColumns, $row);
            $rows[] = $row;
        }
        parent::__construct($rows);
    }

    protected function createResultSeriesObject($resultElem)
    {
        // todo: create metadata stuff
          $resultColumns = $resultElem['columns'];
        $resultValues = $resultElem['values'];
        unset($resultElem['columns']);
        unset($resultElem['values']);
        var_dump($resultElem);
        $seriesElem = new ResultSeriesObject();
        if (isset($resultElem['name'])) {
            $name = $resultElem['name'];
            unset($resultElem['name']);
            $seriesElem->setName($name);
            
        }
        if (count($resultElem)) {
            $seriesElem->setMeta($resultElem);
        }
            
        foreach ($resultValues as $row) {
            if (count($resultColumns) != count($row)) {
                $diffCount = abs(count($resultColumns) - count($row));
                $resultColumns = array_pad($resultColumns, count($row), null);
                $row = array_pad($row, count($resultColumns), null);
            }

            $row = (object) array_combine($resultColumns, $row);
            $rows[] = $row;
        }
        $seriesElem->setRows($rows);
        return $seriesElem;
        
    }
    
}
