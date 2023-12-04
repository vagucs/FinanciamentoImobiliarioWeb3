// SPDX-License-Identifier: MIT
pragma solidity ^0.4.24;

/*
C9 Tech
www.c9coin.com.br
www.c9tech.com.br

- Wagner Nunes
wagner@c9coin.com.br
vagucs@bol.com.br

Este contrato é um MVP, onde 3 pessoas, concordam entre o fiador, financiador e devedor.
Cada NFT pode representar imoveis de areas especificas.

O NFT pode ter detalhes da divida, juros, amortizacao (SAC,PRICE) e evoluir em proporcoes maiores

O Drex e Drex tokenizado nao sao a mesma coisa, o Drex tokenizado, como o nome ja diz, e a autorizacao, futuro,
caso a inadiplencia comprovada se prove, podendo ser, pelo gesto do contrato, transferido ao banco, para execucao de garantia.

- Variacoes possiveis
- Contrato com dois agentes, financiador e devedor e o Drex tokenizado enviado ao gesto do contrato, que e quem definira a liquidacao
  do mesmo em todo o andamento
- Contrato com detalhes da divida, prazo, e possibilidade de gestao e amortizacao variavel com elasticidade e mobilidade da divida
- Contrato com multiplos financiadores ou multiplos garantidores, sendo as fracoes de responsabilidade ja automatizadas
- Contratos unicos, sem padrao definido
- Contratos movidos por tokens de tercerios, o que pode desestabilizar de forma descontrolado o preco de um NFT
- Contratos anexados a contratos cartoriais, onde os devidos saldos sao transferidos mediante comprovacao de total desalienacao do imovel
- Contratos com fontes de dados relativos ao imovel, o que precisa ser feito em blockchain com funcoes de devida privacidade
- Contratos com controle de atrasos e execusoes parciais da garantia, controlado por oraculos ou o proprio agente do contrato como responsavel para tal
- Contratos do tipo precisam de um bom controle sobre a abstracao das carteiras e propriedades dos NFT, assim como a gestao da divida perante
  possiveis renegociacoes.

Observacoes:
    1 - Naturalmente, o imovel passar a ser tokenizado (NFT) e também fracionado pelo token representativo do Drex.
    2 - Contratos sem um padrao definido, são os melhores para controlar situacoes do tipo, usamos o padrao 721 (NFT) pela auto conhecimento e por
        ter sua organizacao e tecnologia ja bem difundida.
    3 - Tantos os bancos quanto o tesouro pode abstrair de oráculos para obter as demandas de tokens de um novo padrao para o financiamento
        imobiliario
    4 - Usando IOT, e possivel usar cartoes Smartcard como a representacao da propriedade do imovel.
    5 - E sempre importante manter uma rotina de fallback tanto para o token nativo, quanto para ERC20 variados que podem ser enviados por
        engano para o contrato. Assim como a gestao e devolucao dos devidos saldo de Drex e Drex tokenizado pelo banco e tesouro, esse MVP
        nao contempla tal operacao mas nao e nada dificil de ser implemtado.
*/

import "./drex.sol";
import "./IERC721.sol";

contract Financial is MyTRC721 {

    /*
    TRC21 é o padrão de tokenização da redt Tomo/Vict, para uso em redes ETH, usar o ERC20 aqui.
    */
    TRC21 public TokenDrex;
    TRC21 public TokenDrexTokenizado;
    address public owner;

    uint256 _tokenIds;

    // Totalizadores permanentes do contrato
    uint256 ValorTotalOfertasInicial;
    uint256 ValorTotalAberto;
    uint256 ValorTotalDividaCorrente;

    struct InfoDebito {
        address agente_financiador; // Representa a figura do banco
        address agente_garantidor; // Representa a figura do tesouro
        address agente_comprador; // Representa o cliente que esta comprando o imóvel
        uint256 valorDoBem;
        uint256 valorDoDebitoAtual;
        uint256 saldoDrex;
        uint256 saldoDrexTokenizado;
    }

    mapping(uint256 => InfoDebito) public RegistroDebitos;

    constructor(address _drexTokenAddress, address _tokenizedDrexTokenAddress) public MyTRC721("FINANCIAL", "FNFT") {
        TokenDrex = TRC21(_drexTokenAddress);
        TokenDrexTokenizado = TRC21(_tokenizedDrexTokenAddress);
        owner = msg.sender;
    }

    // Valor total dos NFT/Imóveis incluidos para o financiamento
    function LerValorTotalEmContrato() external view returns (uint256)
    {
        return ValorTotalOfertasInicial;
    }

    // Valor que ainda não foi financiado do total
    function ValorTotalAbertoEmContrato() external view returns (uint256)
    {
        return ValorTotalAberto;
    }

    // Valor total da divida atual
    function ValorTotalDividaEmContrato() external view returns (uint256)
    {
        return ValorTotalDividaCorrente;
    }

    modifier onlyOwner() {
        require(msg.sender == owner, "Somente o dono pode executar essa funcao");
        _;
    }

    /*
    Efetua a cunhagem do NFT sem especificar o banco, pois o mesmo pode estar em um market place, onde o banco pode escolher
    quais financiamentos pode assumir. O Tesouro pode ter N carteiras, ele como é o garantidor entra com o Drex tokenizado, que no
    caso de inadimplencia total ou parcial, deve ser enviado para o banco o qual pode liquidá-lo da carteira de Drex do tesouro.
    */
    function CriarNFT(address agente_comprador, address agente_garantidor, uint256 valorDoBem) external onlyOwner returns (uint256) {
        uint256 newItemId = ++_tokenIds;
        _mint(agente_comprador, newItemId);

        InfoDebito memory DebitoNFT = InfoDebito({
            agente_financiador: address(0),
            agente_garantidor: agente_garantidor,
            agente_comprador: agente_comprador,
            valorDoBem: valorDoBem,
            valorDoDebitoAtual: valorDoBem,
            saldoDrex: 0,
            saldoDrexTokenizado: 0
        });

        RegistroDebitos[newItemId] = DebitoNFT;

        // Totaliza o valor geral do contrato para todos os NFT cunhados
        ValorTotalOfertasInicial+=valorDoBem;
        ValorTotalAberto+=valorDoBem;
        ValorTotalDividaCorrente+=valorDoBem;

        return newItemId;
    }

    /*
    Qualquer banco, poderia assumir um NFT, sendo ele o unico financiador. Esse contrato pode evoluir para multiplas fontes de financiamento,
    assim como os próprios controles de modo de financiamento e gestão de juros e amortização (SAC, PRICE), mas em se tratando de algo básico
    é o MVP.
    */
    function AssumirComoFinanciador(uint256 tokenId) external {
        require(ownerOf(tokenId) != address(0), "FNFT: Id nao existente");
        InfoDebito storage DebitoNFT = RegistroDebitos[tokenId];
        require(DebitoNFT.agente_financiador == address(0), "FNFT: Banco financiador ja definido para este NFT");
        
        DebitoNFT.agente_financiador = msg.sender;
    }

    /*
    O banco que, anteriormente assume um NFT, está habilitado para fazer o depósito de Drex para o mesmo.
    Incrementando o total da divida do mesmo, esperamos que, o valor do crédito aqui, seja o mesmo da 
    garantia do tesouro. O valor a ser transferido é exatamente o valor da dívida na cunhagem.
    */
    function EnviarDrex(uint256 tokenId) external {
        InfoDebito storage DebitoNFT = RegistroDebitos[tokenId];
        require(msg.sender == DebitoNFT.agente_financiador, "FNT: Apenas o banco financiador pode enviar DREX para o NFT");
        require(DebitoNFT.valorDoDebitoAtual > 0, "FNFT: Nenhuma divida associada ao NFT");
        require(DebitoNFT.saldoDrexTokenizado > 0, "FNFT: Divida ainda nao foi assegurada pelo tesouro");
        require(ValorTotalAberto >= DebitoNFT.valorDoDebitoAtual,"FNFT: Divida ja financiada");

        TokenDrex.transferFrom(DebitoNFT.agente_financiador, DebitoNFT.agente_comprador, DebitoNFT.valorDoDebitoAtual);
        DebitoNFT.saldoDrex += DebitoNFT.valorDoDebitoAtual;

        ValorTotalAberto-=DebitoNFT.valorDoDebitoAtual; // Reduz o valor total da divida aberta, mantendo um saldo de divida ainda não financiada

    }

    /*
    O tesouro, como financiado, precisa depositar o Drex tokenizado, que é sua garantia para o financiador.
    O drex tokenizado, permite acesso ao Drex real em carteira do Tesouro, fornece uma segurança necessária, de que,
    o Drex em garantia está lá e não está sendo usado para outros fins.
    */
    function EnviarDrexTokenizado(uint256 tokenId, uint256 amount) external {
        require(ownerOf(tokenId) != address(0), "FNFT: Operacao em token inexistente");
        InfoDebito storage DebitoNFT = RegistroDebitos[tokenId];
        require(msg.sender == DebitoNFT.agente_garantidor, "FNFT: Apenas o tesouro pode depositar DREX tokenizado");
        require(amount > 0, "FNFT: Quantidade deve ser maior que zero");
        require(DebitoNFT.saldoDrexTokenizado + amount <= DebitoNFT.valorDoBem,"FNFT: Garantia excede o valor do bem");

        TokenDrexTokenizado.transferFrom(msg.sender, address(this), amount);
        DebitoNFT.saldoDrexTokenizado += amount;
    }

    /*
    O pagamento do debito, não vincula ainda a possibilidade de uso de motodologias de financiamento para amortização (SAC, PRICE)
    Isso é um MVP que mostra uma das centenas de possibilidades de uso.
    */
    function PagarDebito(uint256 tokenId, uint256 ValorPago) external {
        require(ownerOf(tokenId) != address(0), "FNFT: Operacao em token inexistente");
        InfoDebito storage DebitoNFT = RegistroDebitos[tokenId];
        require(msg.sender == DebitoNFT.agente_comprador, "FNFT: Apenas o cliente pode pagar a divida");
        require(DebitoNFT.valorDoDebitoAtual > 0, "FNFT: O Imovel encontra-se quitado");
        require(ValorPago <= DebitoNFT.valorDoDebitoAtual, "FNFT: Valor excede a divida atual");

        TokenDrex.transferFrom(DebitoNFT.agente_comprador, DebitoNFT.agente_financiador, ValorPago); // Devolve o valor do Drex para o fiador
        TokenDrexTokenizado.transfer(DebitoNFT.agente_garantidor, ValorPago); // Devolve o Drex tokenizado para o garantidor
        DebitoNFT.valorDoDebitoAtual -= ValorPago;
        DebitoNFT.saldoDrexTokenizado -= ValorPago;
        DebitoNFT.saldoDrex -= ValorPago;
        ValorTotalDividaCorrente -= ValorPago;
    }

    /*
    Com esta função, o emissor do contrato pode enviar o drex tokenizado que está depositado em contrato
    para o financiador, garantindo o recebimento do mesmo.
    Em tratando-se de simulação, vale ressaltar que, apesar de serem aqui, dois tokens distintos, o Drex tokenizado
    carece de uma ferramenta que desembrulhe o Drex tokenizado no saldo de Drex real, o que não
    será feito nessa MVP, pois o controle desse processo é feito pelo BC ou Tesouro nacinal, mas vale lembra,
    que tanto o Drex quanto o Drex tokenizado tem o mesmo valor e segurança da rede.
    */
    function PagarDebitoFiador(uint256 tokenId, uint256 ValorPago) external onlyOwner {
        InfoDebito storage DebitoNFT = RegistroDebitos[tokenId];
        require(DebitoNFT.valorDoDebitoAtual > 0, "FNFT: O Imovel encontra-se quitado");
        require(ValorPago <= DebitoNFT.valorDoDebitoAtual, "FNFT: Valor excede a divida atual");

        TokenDrexTokenizado.transfer(DebitoNFT.agente_financiador, ValorPago); // Devolve o Drex tokenizado para o financiador
        DebitoNFT.valorDoDebitoAtual -= ValorPago;
        DebitoNFT.saldoDrex -= ValorPago;
        ValorTotalDividaCorrente -= ValorPago;
        DebitoNFT.saldoDrexTokenizado -= ValorPago;
    }

    function LerFinanciador(uint256 tokenId) external view returns (address) {
        require(ownerOf(tokenId) != address(0), "FNFT: Consulta de token inexistente");
        return RegistroDebitos[tokenId].agente_financiador;
    }

    function LerGarantidor(uint256 tokenId) external view returns (address) {
        require(ownerOf(tokenId) != address(0), "FNFT: Consulta de token inexistente");
        return RegistroDebitos[tokenId].agente_garantidor;
    }

    function LerComprador(uint256 tokenId) external view returns (address) {
        require(ownerOf(tokenId) != address(0), "FNFT: Consulta de token inexistente");
        return RegistroDebitos[tokenId].agente_comprador;
    }

    function valorDoBem(uint256 tokenId) external view returns (uint256) {
        require(ownerOf(tokenId) != address(0), "FNFT: Consulta de token inexistente");
        return RegistroDebitos[tokenId].valorDoBem;
    }

    function valorDoDebitoAtual(uint256 tokenId) external view returns (uint256) {
        require(ownerOf(tokenId) != address(0), "FNFT: Consulta de token inexistente");
        return RegistroDebitos[tokenId].valorDoDebitoAtual;
    }

    function valorSaldoDrex(uint256 tokenId) external view returns (uint256) {
        require(ownerOf(tokenId) != address(0), "FNFT: Consulta de token inexistente");
        return RegistroDebitos[tokenId].saldoDrex;
    }

    function valorSaldoDrexTokenizado(uint256 tokenId) external view returns (uint256) {
        require(ownerOf(tokenId) != address(0), "FNFT: Consulta de token inexistente");
        return RegistroDebitos[tokenId].saldoDrexTokenizado;
    }

}
