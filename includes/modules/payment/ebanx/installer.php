<?php

/**
 * Copyright (c) 2014, EBANX Tecnologia da Informação Ltda.
 *  All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * Redistributions of source code must retain the above copyright notice, this
 * list of conditions and the following disclaimer.
 *
 * Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation
 * and/or other materials provided with the distribution.
 *
 * Neither the name of EBANX nor the names of its
 * contributors may be used to endorse or promote products derived from
 * this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

class Installer
{
	function stateInstaller($db)
	{
		$db->Execute("update ". TABLE_CONFIGURATION . " set configuration_value='true' where configuration_key='ACCOUNT_STATE_DRAW_INITIAL_DROPDOWN'");
		$check_query = $db->Execute("select zone_id from " . TABLE_ZONES . " where zone_name = 'Rio de Janeiro' limit 1");
		if ($check_query->RecordCount() < 1)
        {
            $maxId   = $db->Execute("select max(zone_id) as zone_id from " . TABLE_ZONES);
            $zone_id = $maxId->fields['zone_id'] + 1;
            $states = $this->getStates();
            foreach ($states as $state)
            {
                $db->Execute("insert into " . TABLE_ZONES . " (zone_id, zone_country_id, zone_code, zone_name) values ('" . $zone_id . "', '30', '" . $state['zone_code'] . "', '" . $state['zone_name'] . "')");
            	$zone_id++;
            }
        }
        else
        {
            return ;
        }
	}


	function getStates()
	{
		return array(
            array(
                'zone_code' => 'AC',
                'zone_name' => 'Acre'         
            ),
            array(
                'zone_code' => 'AL',
                'zone_name' => 'Alagoas'         
            ),
            array(
                'zone_code' => 'AP',
                'zone_name' => 'Amapá'         
            ),
            array(
                'zone_code' => 'AM',
                'zone_name' => 'Amazonas'         
            ),
            array(
                'zone_code' => 'BA',
                'zone_name' => 'Bahia'         
            ),
            array(
                'zone_code' => 'CE',
                'zone_name' => 'Ceará'         
            ),
            array(
                'zone_code' => 'DF',
                'zone_name' => 'Distrito Federal'         
            ),
            array(
                'zone_code' => 'ES',
                'zone_name' => 'Espírito Santo'         
            ),
            array(
                'zone_code' => 'GO',
                'zone_name' => 'Goiás'         
            ),
            array(
                'zone_code' => 'MA',
                'zone_name' => 'Maranhão'         
            ),
            array(
                'zone_code' => 'MT',
                'zone_name' => 'Mato Grosso'         
            ),
            array(
                'zone_code' => 'MS',
                'zone_name' => 'Mato Grosso do Sul'         
            ),
            array(
                'zone_code' => 'MG',
                'zone_name' => 'Minas Gerais'         
            ),
            array(
                'zone_code' => 'PA',
                'zone_name' => 'Pará'         
            ),
            array(
                'zone_code' => 'PB',
                'zone_name' => 'Paraíba'         
            ),
            array(
                'zone_code' => 'PR',
                'zone_name' => 'Paraná'         
            ),
            array(
                'zone_code' => 'PE',
                'zone_name' => 'Pernambuco'         
            ),
            array(
                'zone_code' => 'PI',
                'zone_name' => 'Piauí'         
            ),
            array(
                'zone_code' => 'RJ',
                'zone_name' => 'Rio de Janeiro'         
            ),
            array(
                'zone_code' => 'RN',
                'zone_name' => 'Rio Grande do Norte'         
            ),
            array(
                'zone_code' => 'RS',
                'zone_name' => 'Rio Grande do Sul'         
            ),
            array(
                'zone_code' => 'RO',
                'zone_name' => 'Rondônia'         
            ),
            array(
                'zone_code' => 'RR',
                'zone_name' => 'Roraima'         
            ),
            array(
                'zone_code' => 'SC',
                'zone_name' => 'Santa Catarina'         
            ),
            array(
                'zone_code' => 'SP',
                'zone_name' => 'São Paulo'         
            ),
            array(
                'zone_code' => 'SE',
                'zone_name' => 'Sergipe'         
            ),
            array(
                'zone_code' => 'TO',
                'zone_name' => 'Tocantins'         
            )
    	);
	}
}