<?php

namespace LittleMonkey\Files;

class Files {
	private $uploadFolder;

	private $filesToOptimize = array();
	private $totalFilesToOptimize = 0;

	private $totalAttachments = 0;
	private $currentPage = 1;
	private $postsPerPage = 100;
	private $totalPages = 0;
	private $allowedSizes;


	public function __construct() {
		// Set upload folder
		$this->setUploadFolder();

		// Set total of attachments in system
		$this->setTotalAttachments();

		// Set allowed sites
		$this->setAllowedSizes();
	}

	/**
	 * Get file upload folder from a path
	 *
	 * @param $path
	 *
	 * @return string
	 */
	public function getFileUploadedFolder( $path ) {
		$folder = explode( DIRECTORY_SEPARATOR, $path );

		return $folder[0] . DIRECTORY_SEPARATOR . $folder[1] . DIRECTORY_SEPARATOR;
	}

	/**
	 * Organize files in an array
	 *
	 * @param $attachmentId
	 *
	 * @return array
	 */
	public function setFileSizes( $attachmentId ) {
		$data = wp_get_attachment_metadata( $attachmentId );
		$path = $this->getFileUploadedFolder( $data['file'] );

		$prepared = array(
			'mime_type'         => get_post_mime_type( $attachmentId ),
			'original_filename' => str_replace( $path, '', $data['file'] ),
			'original_path'     => $data['file'],
			'sizes'             => array(),
		);

		foreach ( $data['sizes'] as $size => $info ) {
			$prepared['sizes'][ $size ] = array(
				'filename' => $info['file'],
				'path'     => $path . $info['file'],
			);
		}

		return $prepared;
	}

	/**
	 * Set files to be optimized
	 * Chunked results, to avoid memory limit problems with many posts / attachments
	 *
	 * @return bool
	 */
	public function setFiles() {
		for ( $this->currentPage; $this->currentPage <= $this->totalPages; $this->currentPage ++ ) {
			$args = array(
				'post_type'      => 'attachment',
				'numberposts'    => - 1,
				'post_mime_type' => 'image',
				'post_status'    => 'any',
				'post_parent'    => 'any',
				'posts_per_page' => $this->postsPerPage,
				'orderby'        => 'ID',
				'paged'          => $this->currentPage
			);

			$attachments = new \WP_Query( $args );

			if ( $attachments->have_posts() ) {
				while ( $attachments->have_posts() ) {
					$attachments->the_post();

					// setup files to optimize
					$file = $this->setFileSizes( get_the_ID() );

					// Push original to optimize
					$this->setFileToOptimize( $file['original_path'] );

					foreach ( $file['sizes'] as $name => $value ) {
						if ( in_array( $name, $this->allowedSizes ) ) {
							$this->setFileToOptimize( $value['path'] );
						}
					}
				}
			}

			unset ( $file );
			unset ( $args );
			unset ( $attachments );
		}

		return true;
	}

	/**
	 *
	 * Run same query, to get total results, and create chunk query
	 *
	 */
	public function setTotalAttachments() {
		$args = array(
			'post_type'      => 'attachment',
			'numberposts'    => - 1,
			'post_mime_type' => 'image',
			'post_status'    => 'any',
			'post_parent'    => 'any',
			'posts_per_page' => 1,
			'orderby'        => 'ID',
			'paged'          => 0,
		);

		$result                 = new \WP_Query( $args );
		$this->totalAttachments = $result->found_posts;
		$this->totalPages       = ceil( $this->totalAttachments / $this->postsPerPage );
	}

	/**
	 *
	 * Setup the upload folder base dir
	 *
	 */
	public function setUploadFolder() {
		$this->uploadFolder = wp_upload_dir()['basedir'] . DIRECTORY_SEPARATOR;
	}

	/**
	 *
	 * Get allowed sizes in settings
	 *
	 */
	public function setAllowedSizes() {
		// Allowed sizes
		$this->allowedSizes = json_decode( get_option( 'lm_optimize_sizes' ), true );
	}

	/**
	 * Set file to optimize
	 *
	 * @param $filename
	 *
	 * @return bool
	 */
	public function setFileToOptimize( $filename ) {
		if ( ! in_array( $filename, $this->filesToOptimize ) ) {
			// Increment total files to optimize
			$this->totalFilesToOptimize += 1;

			// Increment total file size
			return $this->filesToOptimize[] = $filename;
		}

		return false;
	}

	public function getFiles() {
		return $this->setFiles();
	}
}
