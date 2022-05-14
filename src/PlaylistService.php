<?php
namespace w3ns0n\YoutubeLaravelApi;

use w3ns0n\YoutubeLaravelApi\Auth\AuthService;
use Exception;

class PlaylistService extends AuthService {

    protected  $setAccessToken;
    public function __construct($googleToken) {
        parent::__construct();
        $this->setAccessToken = $this->setAccessToken($googleToken);
    }

    /**
     * [videosListById description]
     * @param  $part  [snippet,contentDetails,id,statistics](comma separated id's if you want to get more than 1 id details)
     * @param  $params  [regionCode,relevanceLanguage,videoCategoryId, videoDefinition, videoDimension]
     * @return false [description]
     * @throws Exception
     */
    public function getPlaylistsByChannelId($part, $params) {
        try {


            if (! $this->setAccessToken ) {
                return false;
            }

            $params['maxResults'] = 50;
            $params = array_filter($params);


            $service = new \Google_Service_YouTube($this->client);

            return $service->playlists->listPlaylists($part, $params);

        } catch (\Google_Service_Exception $e) {
            throw new Exception($e->getMessage(), 1);

        } catch (\Google_Exception $e) {
            throw new Exception($e->getMessage(), 1);

        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 1);
        }
    }


    public function getPlaylistItemsById($part, $params) {
        try {

            if (! $this->setAccessToken ) {
                return false;
            }
            $params['maxResults'] = 50;
            $params = array_filter($params);


            $service = new \Google_Service_YouTube($this->client);

            return $service->playlistItems->listPlaylistItems($part, $params);

        } catch (\Google_Service_Exception $e) {
            throw new Exception($e->getMessage(), 1);

        } catch (\Google_Exception $e) {
            throw new Exception($e->getMessage(), 1);

        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 1);
        }
    }





}