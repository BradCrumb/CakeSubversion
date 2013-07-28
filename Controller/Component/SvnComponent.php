<?php
class SvnComponent extends Component {

	private $__defaults = array(
		'repository_root' => '',
		'repo_structure' => array(
			'trunk',
			'branches',
			'tags'
		),
		'current_dir' => ''
	);

/**
 * Constructor
 *
 * @param ComponentCollection $collection A ComponentCollection this component can use to lazy load its components
 * @param array $settings Array of configuration settings.
 */
	public function __construct(ComponentCollection $collection, $settings = array()) {
		parent::__construct($collection, $settings);

		$this->defaults($settings);

		if (empty($this->__defaults['repository_root']) && Configure::read('Subversion.SVN.repository_root')) {
			$this->__defaults['repository_root'] = Configure::read('Subversion.Svn.repository_root');
		}
	}

	public function mkdir($repo, $svnLogMessage = '') {
		$return = svn_mkdir($this->fullRepoPath($repo), $svnLogMessage);

		return is_array($return) && count($return) > 0;
	}

	public function fullRepoPath($repo) {
		if (empty($this->__defaults['repository_root'])) {
			throw new CakeException(__('No repository root found'));
		}

		return $this->__defaults['repository_root'] . '/' . $this->__defaults['current_dir'] . '/' . $repo;
	}

	public function changeDir($newDir) {
		$this->__defaults['current_dir'] = $newDir;
	}

	public function defaults(array $options) {
		$this->__defaults = array_merge($this->__defaults, $options);
	}

	public function initStructure(string $repo, $svnLogMessage = 'Initial structure') {
		foreach ($this->__defaults['repo_structure'] as $folder) {
			$this->mkdir($repo . '/' . $folder, $svnLogMessage);
		}
	}

	public function remove($repo, $svnLogMessage = '') {
		svn_remove($this->fullRepoPath($repo));
	}

	public function cat($filePath, $revision = SVN_REVISION_HEAD) {
		return @svn_cat($this->fullRepoPath($filePath), $revision);
	}

	public function ls($repo, $revision = SVN_REVISION_HEAD) {
		return @svn_ls($this->fullRepoPath($repo), $revision);
	}

	public function log($repo, $startRev = SVN_REVISION_HEAD, $endRev = SVN_REVISION_INITIAL, $limit = 0) {
		return @svn_log($this->fullRepoPath($repo), $startRev, $endRev, $limit);
	}
}