<?php

/*
 * Mineur/instagram-parser package
 *
 * Feel free to contribute!
 *
 * @license MIT
 * @author alexhoma <alexcm.14@gmail.com>
 */

namespace Mineur\InstagramParser\Parser;

use Mineur\InstagramParser\Exception\EmptyRequiredParamException;
use Mineur\InstagramParser\Http\HttpClient;
use Mineur\InstagramParser\Model\QueryHash;
use Mineur\InstagramParser\Model\User;

/**
 * Class UserAbstractParser
 *
 * @package Mineur\InstagramParser\Parser
 */
class UserParser
{
    /** Resource endpoint */
    const ENDPOINT = '/%s/?__a=1';
    
    /** @var HttpClient */
    private $httpClient;
	
	/** @var string */
	private $queryHash;
    
    /**
     * InstagramParser constructor.
     *
     * @param HttpClient $httpClient
     * @param QueryHash $queryHash
     */
    public function __construct(
		HttpClient $httpClient,
		QueryHash $queryHash
    ) {
        $this->httpClient = $httpClient;
	    $this->queryHash = $queryHash;
    }
	
	/**
	 * @param string $username
	 *
	 * @return User
	 * @throws EmptyRequiredParamException
	 * @internal param callable|null $callback
	 */
    public function parse(string $username): User
    {
        $this->ensureUsernameIsNotEmpty($username);
        
        $endpoint = sprintf(
            self::ENDPOINT,
            $username
        );
        $response = $this->makeRequest($endpoint, [
			'query' => [
				'query_hash' => $this->queryHash->__toString()
			]
        ]);
        $user     = $response['graphql']['user'];

        return User::fromArray($user);
    }
	
	/**
	 * @param string $endpoint
	 * @param array $options
	 * @return array
	 */
    private function makeRequest(
		string $endpoint,
		array $options
    ): array {
        $response = $this
            ->httpClient
            ->get($endpoint, $options)
        ;
        
        return json_decode((string) $response, true);
    }
    
    /**
     * @param string $username
     * @throws EmptyRequiredParamException
     */
    private function ensureUsernameIsNotEmpty(string $username)
    {
        if (empty($username) || !isset($username)) {
            throw new EmptyRequiredParamException(
                'Username can not be empty.'
            );
        }
    }
}
