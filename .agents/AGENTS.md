# Mandatory Versioning & SSH Deployment Directive: quiz_oralexam

> ⚠️ **STRICT USER DIRECTIVE**: For every single edit, update, bug fix, or feature addition made in any project or plugin in this workspace, you MUST ALWAYS automatically execute the following 4-step workflow without being asked:

---

## 1. 📋 Document Changelog & Bump Version
- Document the update in `README.md` under the `## 📋 Changelog` section with current date and version.
- Increment the version number in `version.php` and update the version badge in `README.md`.

---

## 2. 🔀 Git Push (Master Branch)
- Push changes to GitHub:
  ```bash
  git add .
  git commit -m "<type>(<scope>): <version> <description>"
  git push origin master
  ```

---

## 3. 📦 Package ZIP Artifacts
- Run the packaging script to update ZIP files in `packaged_plugins/`:
  ```powershell
  powershell -ExecutionPolicy Bypass -File "c:\Users\msalem\OneDrive - Energy & Water Academy\Work\Repo\package_moodle_plugins.ps1"
  ```

---

## 4. 🚀 Direct SSH Deployment to Production LMS Server (`150.230.241.37`)
- **Host:** `ubuntu@150.230.241.37`
- **SSH Key:** `C:\Users\msalem\OneDrive - Energy & Water Academy\Documents\ssh-key-2026-07-10 (production lms).key`
- **Plugin Path on Host:** `/home/ubuntu/moodle-project/mod/quiz/report/oralexam/`
- **Full PowerShell Deploy Command:**
  ```powershell
  $sshKey    = "C:\Users\msalem\OneDrive - Energy & Water Academy\Documents\ssh-key-2026-07-10 (production lms).key"
  $remote    = "ubuntu@150.230.241.37"
  $localPath = "c:\Users\msalem\OneDrive - Energy & Water Academy\Work\Repo\oralexam"
  $remotePath = "/home/ubuntu/moodle-project/mod/quiz/report/oralexam/"

  cmd /c "tar -czf - -C `"$localPath`" . | ssh -i `"$sshKey`" -o StrictHostKeyChecking=no $remote `"sudo tar -xzf - -C $remotePath`""
  ssh -i $sshKey -o StrictHostKeyChecking=no $remote "sudo docker exec -u www-data moodle-app php /var/www/html/admin/cli/upgrade.php --non-interactive && sudo docker exec -u www-data moodle-app php /var/www/html/admin/cli/purge_caches.php"
  ```

## 🐳 Docker Container Name
| Environment | Container Name |
|---|---|
| Production | `moodle-app` |
