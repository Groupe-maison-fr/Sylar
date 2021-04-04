# -*- mode: ruby -*-
# vi: set ft=ruby :
#@ansible_home = "/home/vagrant/.ansible"

Vagrant.configure("2") do |config|
    config.vm.define "master" do |subconfig|
        subconfig.vm.box = "bento/ubuntu-18.04"

        subconfig.vm.network "private_network", ip: "192.168.99.20"
        subconfig.vm.network "forwarded_port", guest: 8080, host: 8081
        subconfig.vm.network "forwarded_port", guest: 3306, host: 20306

        authorized_keys = `cat ~/.ssh/id_rsa.pub`
        subconfig.vm.provision :shell, :inline => "echo '#{authorized_keys}' >> /home/vagrant/.ssh/authorized_keys"

        subconfig.vm.provision "ansible_local" do |ansible|
            ansible.raw_arguments = Shellwords.shellsplit(ENV["ANSIBLE_ARGS"]) if ENV["ANSIBLE_ARGS"]
            ansible.galaxy_role_file = 'provisioning/requirements.yml'
            ansible.playbook = "provisioning/vagrant-master.yml"
        end
        subconfig.vm.provider :virtualbox do |vb|
            vb.gui = true
        end
    end

    config.vm.define "slave" do |subconfig|
        subconfig.vm.box = "bento/ubuntu-18.04"

        subconfig.vm.synced_folder "./", "/opt/sylar", type: "nfs", fsnotify: true, exclude: [".idea/", ".git/", "tmp/", "var/", "node_modules/", "vendor/"]

        subconfig.vm.network "private_network", ip: "192.168.99.21"
        subconfig.vm.network "forwarded_port", guest: 8080, host: 8082
        subconfig.vm.network "forwarded_port", guest: 3306, host: 21306
        subconfig.vm.provision "ansible_local" do |ansible|
            ansible.raw_arguments = Shellwords.shellsplit(ENV["ANSIBLE_ARGS"]) if ENV["ANSIBLE_ARGS"]
            ansible.galaxy_role_file = 'provisioning/requirements.yml'
            ansible.playbook = "provisioning/vagrant-slave.yml"
        end

        subconfig.trigger.after :up do |t|
            t.name = "vagrant-fsnotify"
            t.run = { inline: "vagrant fsnotify slave" }
        end

        subconfig.vm.provider :virtualbox do |vb|
            vb.memory = 2048
            vb.gui = true

            for vol in ['1', '2'] do
                diskFilename = File.absolute_path("./.vboxhdd/slave-disk#{vol}.vdi")
                unless File.exist?(diskFilename)
                    vb.customize [
                        'createhd',
                        '--filename', diskFilename,
                        '--size', 50 * 1024
                    ]
                end
                vb.customize [
                    'storageattach', :id,
                    '--storagectl', 'SATA Controller',
                    '--port', vol,
                    '--device', 0,
                    '--type', 'hdd',
                    '--medium', diskFilename
                ]
            end
        end
    end
end
