<?php

namespace w3ns0n\YoutubeLaravelApi;

use w3ns0n\YoutubeLaravelApi\Auth\AuthService;
use Exception;

class ChannelService extends AuthService {

    protected  $setAccessToken;
    public function __construct($googleToken) {
        parent::__construct();
        $this->setAccessToken = $this->setAccessToken($googleToken);
    }
	/**
	 * [channelsListById -gets the channel details and ]
	 * @param  $part    [id,snippet,contentDetails,status, statistics, contentOwnerDetails, brandingSettings]
	 * @param  $params  [array channels id(comma separated ids ) or you can get ('forUsername' => 'GoogleDevelopers')]
	 * @return          [json object of response]
	 */
	public function channelsListById($part, $params) {
		try {

            if (! $this->setAccessToken) {
                return false;
            }

			$params = array_filter($params);

			/**
			 * [$service instance of Google_Service_YouTube]
			 * [$response object of channel lists][making api call to list channels]
			 * @var [type]
			 */

			$service = new \Google_Service_YouTube($this->client);
			$respone = $service->channels->listChannels($part, $params);

			return $service->channels->listChannels($part, $params);

		} catch (\Google_Service_Exception $e) {
			throw new Exception($e->getMessage(), 1);

		} catch (\Google_Exception $e) {
			throw new Exception($e->getMessage(), 1);

		} catch (Exception $e) {
			\Log::info(json_encode($e->getMessage()));
			throw new Exception(json_encode($e->getMessage()), 1);
		}
	}

	public function getChannelDetails($token) {
		try {
            if (! $this->setAccessToken) {
                return false;
            }

            $part = "snippet,contentDetails,statistics,brandingSettings";
			$params = array('mine' => true);
			$service = new \Google_Service_YouTube($this->client);
			$response = $service->channels->listChannels($part, $params);

			$response = json_decode(json_encode($response), true);
			return $response['items'][0];

		} catch (\Google_Service_Exception $e) {
			throw new Exception($e->getMessage(), 1);

		} catch (\Google_Exception $e) {
			throw new Exception($e->getMessage(), 1);

		} catch (Exception $e) {
			throw new Exception($e->getMessage(), 1);
		}
	}
	/**
	 * [updateChannelBrandingSettings update channel details]
	 * @param  $google_token [auth token for the channel]
	 * @param  $properties   ['id' => '',
	 *						          'brandingSettings.channel.description' => '',
	 *						          'brandingSettings.channel.keywords' => '',
	 *						          'brandingSettings.channel.defaultLanguage' => '',
	 *						          'brandingSettings.channel.defaultTab' => '',
	 *						          'brandingSettings.channel.moderateComments' => '',
	 *						          'brandingSettings.channel.showRelatedChannels' => '',
	 *						          'brandingSettings.channel.showBrowseView' => '',
	 *						          'brandingSettings.channel.featuredChannelsTitle' => '',
	 *						          'brandingSettings.channel.featuredChannelsUrls[]' => '',
	 *						          'brandingSettings.channel.unsubscribedTrailer' => '')
	 *						         ]
	 * @param  $part         [ brandingSettings ]
	 * @param  $params       ['onBehalfOfContentOwner' => '']
	 * @return               [boolean ]
	 */
	public function updateChannelBrandingSettings( $properties, $part, $params) {
		try {
            if (! $this->setAccessToken) {
                return false;
            }


            $params = array_filter($params);

			/**
			 * [$service description]
			 * @var [type]
			 */
			$service = new \Google_Service_YouTube($this->client);
			$propertyObject = $this->createResource($properties);

			$resource = new \Google_Service_YouTube_Channel($propertyObject);
			$service->channels->update($part, $resource, $params);

			return true;

		} catch (\Google_Service_Exception $e) {
			throw new Exception($e->getMessage(), 1);

		} catch (\Google_Exception $e) {
			throw new Exception($e->getMessage(), 1);

		} catch (Exception $e) {
			throw new Exception($e->getMessage(), 1);
		}
	}

	/**
	 * [parseSubscriptions working]
	 * @param  [type] $part
	 * @return [type] $params          array('channelId'= '', 'totalResults'= '')
	 */
	public function subscriptionByChannelId($params, $part = 'snippet') {
		try {
            if (! $this->setAccessToken) {
                return false;
            }


            $params = array_filter($params);

			$service = new \Google_Service_YouTube($this->client);
			return $this->parseSubscriptions($params);

		} catch (\Google_Service_Exception $e) {
			throw new Exception($e->getMessage(), 1);

		} catch (\Google_Exception $e) {
			throw new Exception($e->getMessage(), 1);

		} catch (Exception $e) {
			throw new Exception($e->getMessage(), 1);
		}
	}

	/**
	 * [parseSubscriptions working]
	 * @param  [type] $channelId [description]
	 * @return [type]            [description]
	 */
	public function parseSubscriptions($params) {

        if (! $this->setAccessToken) {
            return false;
        }


        $channelId = $params['channelId'];
		$totalResults = $params['totalResults'];
		$maxResultsPerPage = 50;
		if($totalResults < 1){$totalResults = 0;}
		$maxPages = ($totalResults - ($totalResults % $maxResultsPerPage))/$maxResultsPerPage + 1;
		$i = 0;
		try {
			$service = new \Google_Service_YouTube($this->client);
			$part = 'snippet';
			$params = array('channelId' => $channelId, 'maxResults' => $maxResultsPerPage);
			$nextPageToken = 1;
			$subscriptions = [];
			while ($nextPageToken and $i < $maxPages) {
				if($i == $maxPages-1){
					$params['maxResults'] = $totalResults % $maxResultsPerPage + 2;
				}

				$response = $service->subscriptions->listSubscriptions($part, $params);
				$response = json_decode(json_encode($response), true);
				$sub = array_column($response['items'], 'snippet');
				$sub2 = array_column($sub, 'resourceId');
				$subscriptions = array_merge($subscriptions, $sub2);
				$nextPageToken = isset($response['nextPageToken']) ? $response['nextPageToken'] : false;

				$params['pageToken'] = $nextPageToken;
				$i++;
			}

			return $subscriptions;

		} catch (\Google_Service_Exception $e) {
			throw new Exception($e->getMessage(), 1);

		} catch (\Google_Exception $e) {
			throw new Exception($e->getMessage(), 1);

		} catch (Exception $e) {
			throw new Exception($e->getMessage(), 1);
		}

	}

	/**
	 *
	 * properties -  array('snippet.resourceId.kind' => 'youtube#channel','snippet.resourceId.channelId' => 'UCqIOaYtQak4-FD2-yI7hFkw'),
	 * part  = 'snippet'
	 * @param string $value [description]
	 */
	public function addSubscriptions($properties, $token, $part = 'snippet', $params = []) {
		try {

            if (! $this->setAccessToken) {
                return false;
            }

            $service = new \Google_Service_YouTube($this->client);

			$params = array_filter($params);
			$propertyObject = $this->createResource($properties);

			$resource = new \Google_Service_YouTube_Subscription($propertyObject);
			$response = $service->subscriptions->insert($part, $resource, $params);
			return $response;

		} catch (\Google_Service_Exception $e) {
			throw new Exception($e->getMessage(), 1);

		} catch (\Google_Exception $e) {
			throw new Exception($e->getMessage(), 1);

		} catch (Exception $e) {
			throw new Exception($e->getMessage(), 1);
		}

	}

	public function removeSubscription($token, $subscriptionId, $params = []) {
		try {

            if (! $this->setAccessToken) {
                return false;
            }


            $service = new \Google_Service_YouTube($this->client);

			$params = array_filter($params);

			$response = $service->subscriptions->delete($subscriptionId, $params);

		} catch (\Google_Service_Exception $e) {
			throw new Exception($e->getMessage(), 1);

		} catch (\Google_Exception $e) {
			throw new Exception($e->getMessage(), 1);

		} catch (Exception $e) {
			throw new Exception($e->getMessage(), 1);
		}

	}

}