**Repository Management**

*To link a repository:*

git init

git remote add origin <repo\_url>

*To verify the linked remote repository:*

git remote -v

*To check if you're inside a repository:*

git rev-parse --is-inside-work-tree

*To remove link to repository:*

git remote remove origin 

*(Running* git remote -v *afterward should return nothing.)*



**Branch Management**

*To list all local branches and have the branch you are currently in highlighted:*

*(Note that: You are in the master (main) branch of the repository by default until you switch to another.)*

git branch

*To create a new branch:*

*(Note that: creating a new branch DOES NOT switch you to it. You still have to switch after.)*

git branch <branch\_name>

*To switch to a branch:*

git checkout <branch\_name>

*To delete a local branch:*

*(Note that: To delete a local branch, you need to checkout of it.)*

*(Also note that: the second command is to force delete; you may or may not need it.)*

git branch -d <branch-name>

git branch -D <branch-name>



**Committing changes and pushing**

*(Note that: to push to a branch, you may need to pull first.)*

git add <file\_name *OR* . *(for all)*>

git commit -m "<message>"

git push origin <branch\_name>



**Pulling**

git pull origin <branch\_name>

*To proceed regardless of unrelated histories/merge issues (like needing to pull in order to push):*

git pull origin main --allow-unrelated-histories

