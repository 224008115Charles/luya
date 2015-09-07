<?php

namespace luya\commands;

use Yii;

class SetupController extends \luya\base\Command
{
    private function insert($table, $fields)
    {
        return \yii::$app->db->createCommand()->insert($table, $fields)->execute();
    }

    /**
     * @todo use options instead, override options()
     * @todo see if admin is availoable
     *
     * @param string $email
     * @param string $password
     */
    public function actionIndex()
    {
        $email = $this->prompt('Benutzer E-Mail:');
        $password = $this->prompt('Benutzer Passwort:');
        $firstname = $this->prompt('Vorname:');
        $lastname = $this->prompt('Nachname:');
        if (!$this->confirm("Create a new user ($email) with password '$password'?")) {
            exit(1);
        }

        $salt = Yii::$app->getSecurity()->generateRandomString();
        $pw = Yii::$app->getSecurity()->generatePasswordHash($password.$salt);

        $this->insert('admin_user', [
            'title' => 1,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'password' => $pw,
            'password_salt' => $salt,
            'is_deleted' => 0,
        ]);

        $this->insert('admin_group', [
            'name' => 'Adminstrator',
            'text' => 'Administrator Accounts',
        ]);

        $this->insert('admin_user_group', [
            'user_id' => 1,
            'group_id' => 1,
        ]);

        // get the api-admin-user and api-admin-group auth rights
        $data = \yii::$app->db->createCommand("SELECT * FROM admin_auth WHERE api='api-admin-user' OR api='api-admin-group'")->queryAll();

        foreach ($data as $item) {
            $this->insert('admin_group_auth', [
                'group_id' => 1,
                'auth_id' => $item['id'],
                'crud_create' => 1,
                'crud_update' => 1,
                'crud_delete' => 1,
            ]);
        }

        $this->insert('admin_lang', [
            'name' => 'Deutsch',
            'short_code' => 'de',
            'is_default' => 1,
        ]);

        $this->insert('admin_storage_effect', [
            'name' => 'Thumbnail',
            'identifier' => 'thumbnail',
            'imagine_name' => 'thumbnail',
            'imagine_json_params' => json_encode(['vars' => [
                ['var' => 'type', 'label' => 'outbound or inset'], // THUMBNAIL_OUTBOUND & THUMBNAIL_INSET
                ['var' => 'width', 'label' => 'Breit in Pixel'],
                ['var' => 'height', 'label' => 'Hoehe in Pixel'],
            ]]),
        ]);

        $this->insert('admin_storage_effect', [
            'name' => 'Zuschneiden',
            'identifier' => 'resize',
            'imagine_name' => 'resize',
            'imagine_json_params' => json_encode(['vars' => [
                ['var' => 'width', 'label' => 'Breit in Pixel'],
                ['var' => 'height', 'label' => 'Hoehe in Pixel'],
            ]]),
        ]);

        $this->insert('admin_storage_effect', [
            'name' => 'Crop',
            'identifier' => 'crop',
            'imagine_name' => 'crop',
            'imagine_json_params' => json_encode(['vars' => [
                ['var' => 'width', 'label' => 'Breit in Pixel'],
                ['var' => 'height', 'label' => 'Hoehe in Pixel'],
            ]]),
        ]);

        echo "You can now login with E-Mail: '$email' and password: '$password'";
        exit(0);
    }
}
