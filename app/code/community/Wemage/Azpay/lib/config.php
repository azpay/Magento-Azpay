<?php

/**
 * Config Class
 *
 * Static attributes with configurations, operations and messages
 *
 * @author Gabriel Guerreiro <gabrielguerreiro.com>
 * */
class Config {

    /**
     * AZPay URL
     *
     * @var string
     */
    public static $RECEIVER_URL = 'https://api.azpay.com.br/v1/receiver/';

    /**
     * AZPay Version
     *
     * @var string
     */
    public static $AZPAY_VERSION = '1.0.0';

    /**
     * Cards Flags
     * @var array
     */
    public static $FLAGS = array(
        'visa' => 'Visa',
        'mastercard' => 'MasterCard',
        'amex' => 'Amex',
        'elo' => 'Elo',
        'dinners' => 'Dinners',
        'discover' => 'Discover',
        'jcb' => 'JCB',
        'aura' => 'Aura'
    );

    /**
     * AZPay Cards Operators
     *
     * @var array
     */
    public static $CARD_OPERATORS = array(
        'cielo' => array(
            'name' => 'Cielo',
            'modes' => array(
                'store' => array('code' => '1', 'name' => 'Buy Store'),
                'cielo' => array('code' => '2', 'name' => 'Buy Cielo')
            ),
            'flags' => array(
                'visa',
                'mastercard',
                'amex',
                'elo',
                'dinners',
                'discover',
                'jcb',
                'aura'
            )
        ),
        'redecard' => array(
            'name' => 'RedeCard',
            'modes' => array(
                array('code' => '3', 'name' => 'WebService'),
                array('code' => '4', 'name' => 'Integrated')
            ),
            'flags' => array(
                'visa',
                'mastercard'
            )
        ),
        'elavon' => array(
            'code' => '6',
            'name' => 'Elavon',
            'flags' => array(
                'visa',
                'mastercard'
            )
        ),
        'stone' => array(
            'code' => '20',
            'name' => 'Elavon',
            'flags' => array(
                'visa',
                'mastercard'
            )
        )
    );

    /**
     * AZPay Boleto operators
     *
     * @var array
     */
    public static $BOLETO_OPERATORS = array(
        'bradesco' => array(
            'code' => '10',
            'name' => 'Bradesco'
        ),
        'bradesco_eletro' => array(
            'code' => '18',
            'name' => 'Bradesco'
        ),
        'itau' => array(
            'code' => '11',
            'name' => 'Itaú'
        ),
        'banco_do_brasil' => array(
            'code' => '12',
            'name' => 'Banco do Brasil'
        ),
        'santander' => array(
            'code' => '13',
            'name' => 'Santander'
        ),
        'caixa_sem_registro' => array(
            'code' => '14',
            'name' => 'Caixa - Sem Registro'
        ),
        'caixa_sinco' => array(
            'code' => '15',
            'name' => 'Caixa - Sinco'
        ),
        'caixa_sigcb' => array(
            'code' => '16',
            'name' => 'Caixa - SIGCB'
        ),
        'hsbc' => array(
            'code' => '17',
            'name' => 'HSBC'
        )
    );

    /**
     * Currency
     *
     * @var array
     */
    public static $CURRENCIES = array(
        'BRL' => 986
    );

    /**
     * Operation Methods
     *
     * @var array
     */
    public static $OPERATION_METHODS = array(
        '1' => array(
            'name' => 'Crédito a vista'
        ),
        '2' => array(
            'name' => 'Parcelado loja'
        ),
        '3' => array(
            'name' => 'Parcelado administradora'
        ),
        '4' => array(
            'name' => 'Débito'
        )
    );

    /**
     * Code responses by flag
     *
     * @var array
     */
    public static $STATUS = array(
        'CREATED' => 0,
        'AUTHENTICATED' => 1,
        'UNAUTHENTICATED' => 2,
        'AUTHORIZED' => 3,
        'UNAUTHORIZED' => 4,
        'CANCELLING' => 5,
        'CANCELLED' => 6,
        'CAPTURING' => 7,
        'APPROVED' => 8,
        'UNAPPROVED' => 9,
        'SCHEDULED' => 10,
        'GENERATED' => 12
    );

    /**
     * Response messages by status code
     * 
     * @var array
     */
    public static $STATUS_MESSAGES = array(
        0 => array(
            'title' => 'Criada / Em andamento',
            'message' => 'A transação ainda não foi processada pelo Adquirente escolhido para transacionar.',
        ),
        1 => array(
            'title' => 'Autenticada',
            'message' => 'A transação foi validada mais ainda não foi processada para saber se foi aprovada.',
        ),
        2 => array(
            'title' => 'Não Autenticada',
            'message' => 'Não houve validação do cartão de crédito.'
        ),
        3 => array(
            'title' => 'Autorizada pela operadora',
            'message' => 'A transação foi aprovada mais ainda não foi capturada, os créditos ainda não foram creditados para o lojista.'
        ),
        4 => array(
            'title' => 'Não Autorizada pela operadora',
            'message' => 'A transação não foi aprovada, verifique a tabela de possíveis erros.'
        ),
        5 => array(
            'title' => 'Em Cancelamento',
            'message' => 'A transação ainda não foi cancelada, aguardando adquirente retornar a confirmação.'
        ),
        6 => array(
            'title' => 'Cancelado',
            'message' => 'A transação foi cancelada e não será mais creditada o valor para o lojista.'
        ),
        7 => array(
            'title' => 'Em Captura',
            'message' => 'A transação ainda não foi capturada, aguardando adquirente retornar a confirmação.'
        ),
        8 => array(
            'title' => 'Capturada / Finalizada',
            'message' => 'A transação foi capturada e os créditos foram confirmados para o lojista.'
        ),
        9 => array(
            'title' => 'Não Capturada',
            'message' => 'A transação não foi capturada, tentar novamente a captura.'
        ),
        10 => array(
            'title' => 'Pagamento Recorrente - Agendado',
            'message' => 'A transação de pagamento foi agendada para as datas definidas pelo envio do XML.'
        ),
        12 => array(
            'title' => 'Boleto Gerado',
            'message' => 'O boleto foi gerado com sucesso.'
        )
    );

    /**
     * Code operations by flag
     *
     * @var array
     */
    public static $OPERATION = array(
        'AUTHORIZE' => 1,
        'CAPTURE' => 2,
        'SALE' => 3,
        'CANCEL' => 5,
        'REPORT' => 6,
        'REBILL' => 7,
        'BOLETO' => 8,
        'AUTH' => 9,
        'PAGSEGURO' => 10,
        'PAYPAL' => 11,
        'TRANSFER' => 12
    );

    /**
     * Period options to Rebill
     * 
     * @var array
     */
    public static $REBILL_PERIOD = array(
        'DAY' => 1,
        'WEEK' => 2,
        'MONTH' => 3,
        'YEAR' => 4,
    );

    /**
     * Error Messages by code
     *
     * @var array
     */
    public static $ERROR_MESSAGE = array(
        '001' => 'O XML enviado está com erros',
        '002' => 'O XML enviado está com restrições na CIELO',
        '003' => 'O XML enviado está com restrições na REDE',
        '010' => 'Autenticação no AZPAY inválidas',
        '011' => 'Autenticação na CIELO inválidas',
        '012' => 'Autenticação na REDE inválidas',
        '020' => 'Inconsistência no envio do cartão, verifique a modalidade habilitada (BPL)',
        '021' => 'Modalidade de pagamento não habilitada na CIELO',
        '022' => 'Modalidade de pagamento não habilitada na REDE',
        '023' => 'Número de parcelas inválidos ou limite máximo permitido junto à CIELO foi ultrapassado',
        '024' => 'Número de parcelas inválidos ou limite máximo permitido junto à REDE foi ultrapassado',
        '025' => 'Código de segurança ausente ou inválido',
        '026' => 'Erro ao processar o cartão',
        '027' => 'Indicador do código de segurança é inválido',
        '028' => 'Inconsistência no envio do cartão, dados inválidos',
        '030' => 'Código de Transação inválido',
        '031' => 'Identificador, TID, inválido na CIELO',
        '032' => 'Código de Transação inválido na REDE',
        '033' => 'Falha ao cancelar a transação',
        '034' => 'Transação já está cancelada',
        '035' => 'Transação não encontrada para realizar esta operação',
        '036' => 'Transação já está capturada',
        '037' => 'Adquirente informado não está configurado corretamente',
        '100' => 'Falha ao tentar conectar no servidor da operadora - Timeout',
        '101' => 'Transação inválida',
        '900' => 'Moeda inválida',
        '901' => 'Idioma inválido',
        '902' => 'DataHora inválida',
        '903' => 'Número do pedido inválido',
        '904' => 'Valor do pedido inválido',
        '905' => 'Caracteres inválidos no nome do portador',
        '906' => 'Data de validade inválida',
        '907' => 'Bandeira inválida',
        '908' => 'Indicador de gerar token inválido',
        '909' => 'Taxa de embarque inválida',
        '910' => 'Data final é inválida',
        '911' => 'Data inicial é inválida, data precisa ser próximo dia',
        '912' => 'Bandeira utilizada não permite operação de autorização na Cielo'
    );

}

?>