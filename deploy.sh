

rm -r /srv/cid10api/
cp -r ../cid10api /srv/

sudo rm -rf /var/www/redcap/modules/cid10_module_v1.0
sudo cp -r ../cid10ontology/cid10_module_v1.0 /var/www/redcap/modules/
sudo chown -R www-data:www-data /var/www/redcap/modules/cid10_module_v1.0
