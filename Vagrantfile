# -*- mode: ruby -*-
# vi: set ft=ruby :
#@ansible_home = "/home/vagrant/.ansible"

Vagrant.configure("2") do |config|
  config.vm.box = "bento/ubuntu-18.04"

  config.vm.synced_folder "./", "/app"

  config.vm.network "private_network", ip: "192.168.99.20"
  config.vm.network "forwarded_port", guest: 8080, host: 8080

  config.vm.provision "file", source: "~/.ssh/id_rsa.pub", destination: "~/.ssh/me.pub"
  config.vm.provision "shell", inline: "cat ~vagrant/.ssh/me.pub >> ~vagrant/.ssh/authorized_keys"

  config.vm.provision "ansible_local" do |ansible|
    ansible.playbook = "provisioning/vagrant.yml"
  end
end
