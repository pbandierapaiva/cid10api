
echo "Copy API"
rm -r /srv/cid10api/
cp -r ../cid10api /srv/

echo "Continue to copy module to REDCap"
read
sudo rm -rf /var/www/redcap/modules/cid10_module_v1.0
sudo cp -r ../cid10ontology/cid10_module_v1.0 /var/www/redcap/modules/
sudo chown -R www-data:www-data /var/www/redcap/modules/cid10_module_v1.0

echo "Make key bundle for uvicorn"
echo "ARE YOU SURE? CTRL+C"
read
## Quando tiver novo certificado é necessário gerar novamente um uvicorn_cert_bundle.pem
#cat /etc/ssl/certs/unifesp.br/wildcard-unifesp.br.crt /etc/ssl/certs/intermediate.pem > /etc/ssl/certs/unifesp.br/uvicorn_cert_bundle.pem
