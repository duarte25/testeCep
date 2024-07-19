<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CepController extends Controller
{
    public function search($ceps)
    {
        // Separar os Ceps pela vírgula
        $cepArray = explode(',', $ceps);

        $results = [];

        foreach ($cepArray as $cep) {
            // Remover qualquer caractere não numérico
            $cep = preg_replace('/\D/', '', $cep);

            // Consultar a API do ViaCEP
            $response = Http::get("https://viacep.com.br/ws/{$cep}/json/");

            if ($response->ok()) {
                $data = $response->json();
                
                // Reorganizar os dados
                $results[] = [
                    // Removendo caracteres não númericos com Regex
                    'cep' =>  preg_replace('/\D/', '', $data['cep']), 
                    'label' => $data['logradouro'] . ', ' . $data['localidade'],
                    'logradouro' => $data['logradouro'],
                    'complemento' => $data['complemento'],
                    'bairro' => $data['bairro'],
                    'localidade' => $data['localidade'],
                    'uf' => $data['uf'],
                    'ibge' => $data['ibge'],
                    'gia' => $data['gia'],
                    'ddd' => $data['ddd'],
                    'siafi' => $data['siafi']
                ];
            } else {
                // Se o CEP não for encontrado, adicionar um erro ou mensagem padrão
                $results[] = [
                    'cep' => $cep,
                    'error' => 'CEP não encontrado ou inválido'
                ];
            }
        }

        // Retornar a resposta como JSON
        return response()->json($results, 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
