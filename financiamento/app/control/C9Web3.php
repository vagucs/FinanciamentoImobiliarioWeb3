<?php

/*
C9Tech
www.c9tech.com
www.c9coin.com.br

Wagner Nunes
wagner@c9coin.com.br
vagucs@bol.com.br

*/

use Web3\Web3;
use Web3\Contract;
use Web3p\EthereumTx\Transaction;
use Web3\Utils;

class C9Web3
{
    private $contractAddress;
    private $contractABI;
    private $contract;
    private $rpc;
    private $web3;
    private $eth;
    private $nonce;
    private $gasprice;
    private $gaslimit;
    private $hash;
    private $valid;
    private $nft;
    private $blocknumber;
    private $trstatus = 0;

    public function __construct()
    {
        $this->contractABI = file_get_contents('/var/www/html/financiamento/nft.abi');
        $this->contractAddress = '0x57a7ea82ebd5e50259cd3088cf0e6b52d4a2fcba'; // Deixamos fixo em se tratar de um MVP, mas pode ser obtido do front que comporta o mesmo
        $this->rpc = 'https://rpc.testnet.tomochain.com'; // RPC da rede teste Tomo/Vict

        $this->web3 = new Web3($this->rpc);
        $this->eth =  $this->web3->eth;

        $this->contract = new Contract($this->web3->provider, $this->contractABI);

    }

    public function LerValorTotalEmContrato()
    {
        $ret = '';
        $this->contract->at($this->contractAddress)->call('LerValorTotalEmContrato', function ($err, $result) use (&$ret) {

                    if ($err !== null) {
                        return;
                    }
                    $ret=C9Web3::w3monetario($result[0]);
                });
        return $ret;
    }

    public function ValorTotalAbertoEmContrato()
    {
        $ret = '';
        $this->contract->at($this->contractAddress)->call('ValorTotalAbertoEmContrato', function ($err, $result) use (&$ret){
                    if ($err !== null) {
                        return;
                    }
                    $ret=C9Web3::w3monetario($result[0]);
                });
        return $ret;
}

    public function ValorTotalDividaEmContrato()
    {
        $ret = '';
        $this->contract->at($this->contractAddress)->call('ValorTotalDividaEmContrato', function ($err, $result) use (&$ret) {
                    if ($err !== null) {
                        return;
                    }
                    $ret=C9Web3::w3monetario($result[0]);
                });
        return $ret;
    }

    public function CriarNFT($privateKey,$fromAddress,$agente_comprador,$agente_garantidor,$valorDoBem)
    {

        $this->nonce=-1;

        $this->eth->getTransactionCount($fromAddress,function($err,$res)
        {

            if ($err !== null) {

                return false;
            }

            $this->nonce=$res->value;
            return $res;
        });

        if ($this->nonce==-1)
        {
            return false;
        }


        $nonce = Utils::toHex((string)$this->nonce);

        $this->eth->gasPrice(function ($err, $gasPrice) {
            if ($err !== null) {
                return false;
            }
            $this->gasprice=$gasPrice->toString();
        });

        $gasPrice = Utils::toHex($this->gasprice);
        $gasLimit = Utils::toHex(100000000);

        $valorDoBem = bcmul($valorDoBem, bcpow('10', '18'));
        $valorDoBem = Utils::toHex($valorDoBem, true);
        $valorDoBem = Utils::toBn($valorDoBem);

        $function = $this->contract->at($this->contractAddress)->getData('CriarNFT', $agente_comprador,$agente_garantidor,$valorDoBem);

        $transaction = new Transaction([
            'nonce' => '0x' . $nonce,
            'gasPrice' => '0x' . $gasPrice,
            'gasLimit' => '0x' . $gasLimit,
            'to' => $this->contractAddress,
            'from' => $fromAddress,
            'value' => Utils::toHex(0),
            'data' => '0x' . $function,
            'chainId' => 89
        ]);

        $signedTransaction = $transaction->sign($privateKey);

        $this->hash='';


        $this->eth->sendRawTransaction('0x' . $signedTransaction, function ($err, $tx) {

            if ($err !== null) {

                return false;
            }

            $this->hash=$tx;
        });

        $this->blocknumber=0;

        $this->trstatus=-1;

        if (!empty($this->hash))
        {
            for($i=0;$i<30;$i++)
            {
                $this->eth->getTransactionReceipt($this->hash, function ($err, $transaction) {
                    if ($err !== null) {
                        return $this->fail($err->getMessage());
                    }

                    if (gettype($transaction)==='object')
                    {
                        $this->nft = hexdec(substr($transaction->logs[0]->topics[3],-16)); // Indice do NFT
                        $this->valid = $transaction->logs[0]->removed;
                        $this->blocknumber = hexdec(substr($transaction->blockNumber,-16));
                        $this->trstatus = $transaction->status;
                    }

                });

                if (!($this->trstatus===-1))
                {
                    if ($this->trstatus==='0x1')
                    {
                        break;
                    }else{
                        break;
                    }
                }
                sleep(1);
            }
        }

        if ($this->trstatus==='0x1')
        {
            return [$this->nft,$this->blocknumber,$this->hash];
        }else{
            return false;
        }

    }

    public function AssumirComoFinanciador($privateKey,$fromAddress,$tokenId)
    {
        $this->nonce=-1;
        $this->eth->getTransactionCount($fromAddress,function($err,$res)
        {
            if ($err !== null) {
                return false;
            }
            $this->nonce=$res->value;
            return $res;
        });

        if ($this->nonce==-1)
        {
            return false;
        }

        $nonce = Utils::toHex((string)$this->nonce);

        $this->eth->gasPrice(function ($err, $gasPrice) {
            if ($err !== null) {
                return false;
            }
            $this->gasprice=$gasPrice->toString();
        });

        $gasPrice = Utils::toHex($this->gasprice);
        $gasLimit = Utils::toHex(100000000);

        $function = $this->contract->at($this->contractAddress)->getData('AssumirComoFinanciador', $tokenId);

        $transaction = new Transaction([
            'nonce' => '0x' . $nonce,
            'gasPrice' => '0x' . $gasPrice,
            'gasLimit' => '0x' . $gasLimit,
            'to' => $this->contractAddress,
            'from' => $fromAddress,
            'value' => Utils::toHex(0),
            'data' => '0x' . $function,
            'chainId' => 89
        ]);

        $signedTransaction = $transaction->sign($privateKey);

        $this->hash='';

        $this->eth->sendRawTransaction('0x' . $signedTransaction, function ($err, $tx) {

            if ($err !== null) {
                return false;
            }
            $this->hash=$tx;
        });

        $this->blocknumber=0;

        $this->trstatus=-1;

        if (!empty($this->hash))
        {
            for($i=0;$i<30;$i++)
            {
                $this->eth->getTransactionReceipt($this->hash, function ($err, $transaction) {
                    if ($err !== null) {
                        return $this->fail($err->getMessage());
                    }

                    if (gettype($transaction)==='object')
                    {
                        $this->trstatus = $transaction->status;
                    }

                });

                if (!($this->trstatus===-1))
                {
                    if ($this->trstatus==='0x1')
                    {
                        break;
                    }else{
                        break;
                    }
                }
                sleep(1);
            }

        }

        if ($this->trstatus==='0x1')
        {
            return [$this->nft,$this->blocknumber,$this->hash];
        }else{
            return false;
        }
    }

    public function EnviarDrex($privateKey,$fromAddress,$tokenId)
    {

        $this->nonce=-1;

        $this->eth->getTransactionCount($fromAddress,function($err,$res)
        {
            if ($err !== null) {
                return false;
            }

            $this->nonce=$res->value;
            return $res;
        });

        if ($this->nonce==-1)
        {
            return false;
        }

        $nonce = Utils::toHex((string)$this->nonce);

        $this->eth->gasPrice(function ($err, $gasPrice) {
            if ($err !== null) {
                return false;
            }
            $this->gasprice=$gasPrice->toString();
        });

        $gasPrice = Utils::toHex($this->gasprice);
        $gasLimit = Utils::toHex(100000000);

        $function = $this->contract->at($this->contractAddress)->getData('EnviarDrex', $tokenId);

        $transaction = new Transaction([
            'nonce' => '0x' . $nonce,
            'gasPrice' => '0x' . $gasPrice,
            'gasLimit' => '0x' . $gasLimit,
            'to' => $this->contractAddress,
            'from' => $fromAddress,
            'value' => Utils::toHex(0),
            'data' => '0x' . $function,
            'chainId' => 89
        ]);

        $signedTransaction = $transaction->sign($privateKey);

        $this->hash='';

        $this->eth->sendRawTransaction('0x' . $signedTransaction, function ($err, $tx) {
            if ($err !== null) {
                return false;
            }

            $this->hash=$tx;
        });

        $this->blocknumber=0;

        $this->trstatus=-1;

        if (!empty($this->hash))
        {
            for($i=0;$i<30;$i++)
            {
                $this->eth->getTransactionReceipt($this->hash, function ($err, $transaction) {
                    if ($err !== null) {
                        return $this->fail($err->getMessage());
                    }

                    if (gettype($transaction)==='object')
                    {
                        $this->trstatus = $transaction->status;
                    }
                });

                if (!($this->trstatus===-1))
                {
                    if ($this->trstatus==='0x1')
                    {

                        break;
                    }else{

                        break;
                    }
                }
                sleep(1);
            }

        }

        if ($this->trstatus==='0x1')
        {
            return [$this->nft,$this->blocknumber,$this->hash];
        }else{
            return false;
        }
    }

    public function EnviarDrexTokenizado($privateKey,$fromAddress,$tokenId,$valorDoBem)
    {

        $this->nonce=-1;

        $this->eth->getTransactionCount($fromAddress,function($err,$res)
        {
            if ($err !== null) {
                return false;
            }
            $this->nonce=$res->value;
            return $res;
        });

        if ($this->nonce==-1)
        {
            return false;
        }

        $nonce = Utils::toHex((string)$this->nonce);

        $this->eth->gasPrice(function ($err, $gasPrice) {
            if ($err !== null) {
                return false;
            }
            $this->gasprice=$gasPrice->toString();
        });

        $gasPrice = Utils::toHex($this->gasprice);
        $gasLimit = Utils::toHex(100000000);

        $valorDoBem = bcmul($valorDoBem, bcpow('10', '18'));
        $valorDoBem = Utils::toHex($valorDoBem, true);
        $valorDoBem = Utils::toBn($valorDoBem);

        $function = $this->contract->at($this->contractAddress)->getData('EnviarDrexTokenizado', $tokenId, $valorDoBem);

        $transaction = new Transaction([
            'nonce' => '0x' . $nonce,
            'gasPrice' => '0x' . $gasPrice,
            'gasLimit' => '0x' . $gasLimit,
            'to' => $this->contractAddress,
            'from' => $fromAddress,
            'value' => Utils::toHex(0),
            'data' => '0x' . $function,
            'chainId' => 89
        ]);

        $signedTransaction = $transaction->sign($privateKey);

        $this->hash='';


        $this->eth->sendRawTransaction('0x' . $signedTransaction, function ($err, $tx) {
            if ($err !== null) {
                return false;
            }
            $this->hash=$tx;
        });

        $this->blocknumber=0;

        $this->trstatus=-1;

        if (!empty($this->hash))
        {
            for($i=0;$i<30;$i++)
            {
                $this->eth->getTransactionReceipt($this->hash, function ($err, $transaction) {
                    if ($err !== null) {
                        return $this->fail($err->getMessage());
                    }

                    if (gettype($transaction)==='object')
                    {
                        $this->trstatus = $transaction->status;
                    }
                });

                if (!($this->trstatus===-1))
                {
                    if ($this->trstatus==='0x1')
                    {

                        break;
                    }else{

                        break;
                    }
                }
                sleep(1);
            }

        }

        if ($this->trstatus==='0x1')
        {
            return [$this->nft,$this->blocknumber,$this->hash];
        }else{
            return false;
        }

    }

    public function PagarDebito($privateKey,$fromAddress,$tokenId,$ValorPago)
    {
        $this->nonce=-1;
        $this->eth->getTransactionCount($fromAddress,function($err,$res)
        {
            if ($err !== null) {
                return false;
            }
            $this->nonce=$res->value;
            return $res;
        });

        if ($this->nonce==-1)
        {
            return false;
        }

        $nonce = Utils::toHex((string)$this->nonce);

        $this->eth->gasPrice(function ($err, $gasPrice) {
            if ($err !== null) {
                return false;
            }
            $this->gasprice=$gasPrice->toString();
        });

        $gasPrice = Utils::toHex($this->gasprice);
        $gasLimit = Utils::toHex(100000000);

        $ValorPago = bcmul($ValorPago, bcpow('10', '18'));
        $ValorPago = Utils::toHex($ValorPago, true);
        $ValorPago = Utils::toBn($ValorPago);

        $function = $this->contract->at($this->contractAddress)->getData('PagarDebito', $tokenId, $ValorPago);

        $transaction = new Transaction([
            'nonce' => '0x' . $nonce,
            'gasPrice' => '0x' . $gasPrice,
            'gasLimit' => '0x' . $gasLimit,
            'to' => $this->contractAddress,
            'from' => $fromAddress,
            'value' => Utils::toHex(0),
            'data' => '0x' . $function,
            'chainId' => 89
        ]);

        $signedTransaction = $transaction->sign($privateKey);

        $this->hash='';

        $this->eth->sendRawTransaction('0x' . $signedTransaction, function ($err, $tx) {

            if ($err !== null) {
                return false;
            }

            $this->hash=$tx;
        });

        $this->blocknumber=0;

        $this->trstatus=-1;

        if (!empty($this->hash))
        {
            for($i=0;$i<30;$i++)
            {
                $this->eth->getTransactionReceipt($this->hash, function ($err, $transaction) {
                    if ($err !== null) {
                        return $this->fail($err->getMessage());
                    }
                    if (gettype($transaction)==='object')
                    {
                        $this->trstatus = $transaction->status;
                    }
                });

                if (!($this->trstatus===-1))
                {
                    if ($this->trstatus==='0x1')
                    {
                        break;
                    }else{

                        break;
                    }
                }
                sleep(1);
            }
        }

        if ($this->trstatus==='0x1')
        {
            return [$this->nft,$this->blocknumber,$this->hash];
        }else{
            return false;
        }

    }

    public function PagarDebitoFiador($privateKey,$fromAddress,$tokenId,$ValorPago)
    {
        $this->nonce=-1;
        $this->eth->getTransactionCount($fromAddress,function($err,$res)
        {
            if ($err !== null) {

                return false;
            }
            $this->nonce=$res->value;
            return $res;
        });

        if ($this->nonce==-1)
        {
            return false;
        }

        $nonce = Utils::toHex((string)$this->nonce);

        $this->eth->gasPrice(function ($err, $gasPrice) {
            if ($err !== null) {
                return false;
            }
            $this->gasprice=$gasPrice->toString();
        });

        $gasPrice = Utils::toHex($this->gasprice);
        $gasLimit = Utils::toHex(100000000);

        $ValorPago = bcmul($ValorPago, bcpow('10', '18'));
        $ValorPago = Utils::toHex($ValorPago, true);
        $ValorPago = Utils::toBn($ValorPago);

        $function = $this->contract->at($this->contractAddress)->getData('PagarDebitoFiador', $tokenId, $ValorPago);

        $transaction = new Transaction([
            'nonce' => '0x' . $nonce,
            'gasPrice' => '0x' . $gasPrice,
            'gasLimit' => '0x' . $gasLimit,
            'to' => $this->contractAddress,
            'from' => $fromAddress,
            'value' => Utils::toHex(0),
            'data' => '0x' . $function,
            'chainId' => 89
        ]);

        $signedTransaction = $transaction->sign($privateKey);

        $this->hash='';


        $this->eth->sendRawTransaction('0x' . $signedTransaction, function ($err, $tx) {
            if ($err !== null) {
                return false;
            }
            $this->hash=$tx;
        });

        $this->blocknumber=0;

        $this->trstatus=-1;

        if (!empty($this->hash))
        {
            for($i=0;$i<30;$i++)
            {
                $this->eth->getTransactionReceipt($this->hash, function ($err, $transaction) {
                    if ($err !== null) {
                        return $this->fail($err->getMessage());
                    }

                    if (gettype($transaction)==='object')
                    {
                        $this->trstatus = $transaction->status;
                    }
                });

                if (!($this->trstatus===-1))
                {
                    if ($this->trstatus==='0x1')
                    {
                        break;
                    }else{

                        break;
                    }
                }
                sleep(1);
            }

        }

        if ($this->trstatus==='0x1')
        {
            return [$this->nft,$this->blocknumber,$this->hash];
        }else{
            return false;
        }

    }

    public function LerFinanciador($tokenId)
    {
        $ret = '';
        $this->contract->at($this->contractAddress)->call('LerFinanciador',$tokenId, function ($err, $result) use (&$ret) {
                    if ($err !== null) {
                        return;
                    }
                    $ret = $result[0];
                });
        return $ret;
    }

    public function LerGarantidor($tokenId)
    {
        $ret = '';
        $this->contract->at($this->contractAddress)->call('LerGarantidor',$tokenId, function ($err, $result) use (&$ret) {
                    if ($err !== null) {
                        return;
                    }
                    $ret = $result[0];
                });
        return $ret;
    }

    public function LerComprador($tokenId)
    {
        $ret = '';
        $this->contract->at($this->contractAddress)->call('LerComprador',$tokenId, function ($err, $result) use (&$ret) {
                    if ($err !== null) {
                        return;
                    }
                    $ret = $result[0];
                });
        return $ret;
    }

    public function valorDoBem($tokenId)
    {
        $ret='';
        $this->contract->at($this->contractAddress)->call('valorDoBem',$tokenId, function ($err, $result) use (&$ret){
                    if ($err !== null) {
                        return;
                    }
                    $ret=C9Web3::w3monetario($result[0]);
                });
        return $ret;
    }

    static function w3monetario($x)
    {
        $dec=substr($x,-18);
        $x=substr($x,0,strlen($x)-18);
        $valor=$x . '.' . $dec;
        return (float) $valor;
    }

    public function valorDoDebitoAtual($tokenId)
    {
        $ret = '';
        $this->contract->at($this->contractAddress)->call('valorDoDebitoAtual',$tokenId, function ($err, $result) use (&$ret) {
                    if ($err !== null) {
                        return;
                    }
                    $ret=C9Web3::w3monetario($result[0]);
                });
        return $ret;
    }

    public function valorSaldoDrex($tokenId)
    {
        $ret = '';
        $this->contract->at($this->contractAddress)->call('valorSaldoDrex',$tokenId, function ($err, $result) use (&$ret) {
                    if ($err !== null) {
                        return;
                    }
                    $ret=C9Web3::w3monetario($result[0]);
                });
        return $ret;
    }

    public function valorSaldoDrexTokenizado($tokenId)
    {
        $ret = '';
        $this->contract->at($this->contractAddress)->call('valorSaldoDrexTokenizado',$tokenId, function ($err, $result) use (&$ret) {
                    if ($err !== null) {
                        return;
                    }
                    $ret=C9Web3::w3monetario($result[0]);
                });
        return $ret;
    }

}