<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;



class ComparaStringController  extends Controller
{

    public function compararString(Request $request){
        $primeiraString = filter_var($request->get('primeira'), FILTER_SANITIZE_STRING);
        $segundaString = filter_var($request->get('segunda'), FILTER_SANITIZE_STRING);

        $diferenca = $this->diferenca($primeiraString, $segundaString);

        return response()->json([
            'diferenca' => $diferenca
        ]);

    }
    public function diferenca($primeiraString, $segundaString)
    {

        $diferenca = $this->juntaDiferenca(str_split($primeiraString), str_split($segundaString));
        $diferencaValor = $diferenca['values'];
        $diferencaMascara = $diferenca['mask'];

        $qtd = count($diferencaValor);
        $posicaoIni = 0;
        $result = '';
        for ($i = 0; $i < $qtd; $i++)
        {
            $posicao = $diferencaMascara[$i];

            if ($posicao != $posicaoIni)
            {
                switch ($posicaoIni)
                {
                    case -1: $result ; break;
                    case 1: $result .= '</ins>'; break;
                }
                switch ($posicao)
                {
                    case -1: $result; break;
                    case 1: $result .= '<ins class="text-danger">'; break;
                }
            }
            $result .= $diferencaValor[$i];

            $posicaoIni = $posicao;
        }

        switch ($posicaoIni)
        {
            case -1: $result ; break;
            case 1: $result .= '</ins>'; break;
        }

        return $result;
    }

   public function juntaDiferenca($primeira, $segunda)
    {
        $diferencaValores = [];
        $mascara = [];

        $dm = [];
        $s1 = count($primeira);
        $s2 = count($segunda);

        for ($i = -1; $i < $s2; $i++)
        {
            $dm[-1][$i] = 0;
        }

        for ($i = -1; $i < $s1; $i++)
        {
            $dm[$i][-1] = 0;
        }

        for ($i = 0; $i < $s1; $i++)
        {
            for ($j = 0; $j < $s2; $j++)
            {
                if ($primeira[$i] == $segunda[$j])
                {
                    $ad = $dm[$i - 1][$j - 1];
                    $dm[$i][$j] = $ad + 1;
                }
                else
                {
                    $a1 = $dm[$i - 1][$j];
                    $a2 = $dm[$i][$j - 1];
                    $dm[$i][$j] = max($a1, $a2);
                }
            }
        }

        $i = $s1 - 1;
        $j = $s2 - 1;
        while (($i > -1) || ($j > -1))
        {
            if ($j > -1)
            {
                if ($dm[$i][$j - 1] == $dm[$i][$j])
                {
                    $diferencaValores[] = $segunda[$j];
                    $mascara[] = 1;
                    $j--;
                    continue;
                }
            }
            if ($i > -1)
            {
                if ($dm[$i - 1][$j] == $dm[$i][$j])
                {

                    $diferencaValores[] = $primeira[$i];
                    $mascara[] = -1;
                    $i--;
                    continue;
                }
            }
            {
                $diferencaValores[] = $primeira[$i];
                $mascara[] = 0;
                $i--;
                $j--;
            }
        }

        $diferencaValores = array_reverse($diferencaValores);
        $mascara = array_reverse($mascara);
        return ['values' => $diferencaValores, 'mask' => $mascara];
    }

}
